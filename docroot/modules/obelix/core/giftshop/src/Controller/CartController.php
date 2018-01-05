<?php

/**
 * @file
 * The page controller for all giftshop cart actions.
 */

namespace Drupal\giftshop\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Url;
use Drupal\file\Entity\File;
use Drupal\file\FileInterface;
use Drupal\giftshop\GiftshopCart;
use Drupal\giftshop\GiftshopCartInterface;
use Drupal\giftshop\GiftshopCartItemInterface;
use Drupal\giftshop\GiftshopCartTempItem;
use Drupal\giftshop\GiftshopCartTempItemInterface;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class CartController.
 *
 * @package Drupal\giftshop\Controller
 */
class CartController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * @var GiftshopCart $giftshopCart
   */
  protected $giftshopCart;

  /**
   * @var GiftshopCartTempItem $giftshopCartTempItem
   */
  protected $giftshopCartTempItem;

  /**
   * @var ModuleHandlerInterface $moduleHandler.
   */
  protected $moduleHandler;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('giftshop.cart'),
      $container->get('giftshop.cart.temp'),
      $container->get('module_handler')
    );
  }

  /**
   * CartController constructor.
   *
   * @param \Drupal\giftshop\GiftshopCartInterface $giftshopCart
   *   The Giftshop Cart Service.
   * @param \Drupal\giftshop\GiftshopCartTempItemInterface $giftshopCartTempItem
   *   The temporary giftshop cart item.
   */
  public function __construct(GiftshopCartInterface $giftshopCart, GiftshopCartTempItemInterface $giftshopCartTempItem,
    ModuleHandlerInterface $moduleHandler) {

    $this->giftshopCart = $giftshopCart;
    $this->giftshopCartTempItem = $giftshopCartTempItem;
    $this->moduleHandler = $moduleHandler;
  }

  /**
   * Builds the cart overview page.
   *
   * @return array
   *   An renderable array
   */
  public function viewItems() {
    $items = [];

    /**
     * @var GiftshopCartItemInterface $item
     */
    foreach ($this->giftshopCart->getItems() as $index => $item) {
      if ($gift_node = Node::load($item->getNodeId())) {
        $description = [];
        if (!$gift_node->field_gift_description->isEmpty()) {
          $description = [
            '#markup' => $gift_node->field_gift_description[0]->value,
          ];
        }

        $image = [];
        if ($file = self::getSlideshowSlideImage($gift_node)) {
          $image = self::buildRenderableImage($file);
        }

        $actions = [];
        $actions['remove'] = [
          '#type' => 'link',
          '#title' => $this->t('Remove gift'),
          '#url' => Url::fromRoute('giftshop.cart.remove', [
            'index' => $index,
          ], [
            'query' => [
              'hash' => $item->getUuid(),
            ]
          ]),
        ];

        // Append item.
        $items[$index] = [
          '#theme' => 'giftshop_cart_item',
          '#image' => $image,
          '#name' => $gift_node->label(),
          '#description' => $description,
          '#quantity' => $item->getQuantity(),
          '#price' => $item->getTotalPrice(),
          '#actions' => [
            '#theme' => 'item_list',
            '#items' => $actions,
            '#attributes' => [
              'class' => [
                'list-inline',
              ],
            ],
          ],
        ];
      }
      else {
        // Could not load gift node, remove item from cart.
        $this->giftshopCart->removeItem($index, $item->getUuid());
      }
    }

    // Check if broker_id exists and if yes,
    // change the "Select another gift" button to the broker page.
    $other_gifts_url = 'internal:/node/625';

    $user_private_store = \Drupal::service('user.private_tempstore')->get('basket_redirect');
    $entity_path = $user_private_store->get('entity_path');
    if ($entity_path) {
      $other_gifts_url = 'internal:/' . $entity_path;
    }

    $actions = [];
    $actions['continue'] = [
      '#title' => $this->t('Select another gift'),
      '#type' => 'link',
      '#url' => Url::fromUri($other_gifts_url),
      '#attributes' => [
        'class' => [
          'btn',
          'btn-default',
          'btn-beauty',
          'btn-transparent',
          'btn-fullsize-xs',
        ],
      ],
    ];
    $actions['checkout'] = [
      '#title' => $this->t('Present now'),
      '#type' => 'link',
      '#url' => Url::fromUri('internal:/formulare/das-gute-geschenk-checkout'),
      '#attributes' => [
        'class' => [
          'btn',
          'btn-default',
          'btn-beauty',
          'btn-fullsize-xs',
        ],
      ],
    ];

    $build = [
      '#theme' => 'giftshop_cart',
      '#headline' => $this->t('Your giftcart'),
      '#items' => $items,
      '#actions' => $actions,
      '#total' => $this->giftshopCart->getTotalPrice(),
      '#attached' => [
        'library' => ['beaufix/giftshop']
      ],
      '#cache' => ['max-age' => 0],
    ];

    if ($this->moduleHandler->moduleExists('transparency_bar')) {
      $build['#transparency_bar'] = [
        '#theme' => 'editorial_transparency_bar__type_01',
      ];
    }

    // Let other modules alter cart.
    $this->moduleHandler->alter('cart_build', $build);

    return $build;
  }

  /**
   * Remove an item from the cart and redirects back to the cart overview.
   *
   * @param $index
   *   The index of the item to remove.
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   *   A redirect response to the giftshop cart overview.
   */
  public function removeItem($index) {
    $uuid = \Drupal::request()->get('hash');
    if ($this->giftshopCart->removeItem($index, $uuid)) {
      drupal_set_message($this->t('Item successfully removed.'));
    }

    return $this->redirect('giftshop.cart', [], [], 301);
  }

  /**
   * Adds the temp item to the cart.
   */
  public function addItem() {
    if ($node_id = $this->giftshopCartTempItem->getNodeId()) {
      $content = [];

      $node = Node::load($node_id);

      $content['certificate'] = [];

      if ($target_id = $node->field_gift_certificate_preview[0]->target_id) {
        $content['certificate']['image'] = self::buildRenderableImage(File::load($target_id));
        $content['certificate']['image']['#attributes']['class'][] = 'center-block';
      }

      $content['certificate']['headline'] = [
        '#markup' => $this->t('certificate'),
      ];
      $content['certificate']['copy'] = [
        '#markup' => t('The certificate reaches you in various ways - Choose between Email or Post.'),
      ];
      $content['certificate']['button'] = [
        '#title' => $this->t('Choose'),
        '#type' => 'link',
        '#url' => Url::fromRoute('giftshop.cart.add.cert'),
        '#attributes' => [
          'class' => [
            'btn',
            'btn-default',
            'btn-beauty',
            'btn-fullsize-xs',
            'btn-transparent',
          ],
        ],
      ];

      $content['card']['module_path'] = base_path() . drupal_get_path('module', 'giftshop');
      $content['card']['headline'] = [
        '#markup' => $this->t('Greetings card'),
      ];
      $content['card']['copy'] = [
        '#markup' => $this->t('We send the greeting card to you by mail or directly to the recipient.'),
      ];
      $content['card']['button'] = [
        '#title' => $this->t('Choose'),
        '#type' => 'link',
        '#url' => Url::fromRoute('giftshop.cart.add.card'),
        '#attributes' => [
          'class' => [
            'btn',
            'btn-default',
            'btn-beauty',
            'btn-transparent',
            'btn-fullsize-xs',
          ],
        ],
      ];

      return [
        '#theme' => 'giftshop_gift_response_type',
        '#attributes' => [],
        '#content' => $content,
      ];
    }

    return new RedirectResponse(Url::fromRoute('giftshop.cart')->toString());
  }

  /**
   * Builds a renderable array for an image.
   *
   * @param \Drupal\file\FileInterface $file
   *   The image file entity.
   * @param string $image_style
   *   The image style to render the image.
   *
   * @return array
   *   The image as renderable array.
   */
  public static function buildRenderableImage(FileInterface $file, $image_style = 'large') {
    $variables = array(
      'style_name' => $image_style,
      'uri' => $file->getFileUri(),
    );

    $image = \Drupal::service('image.factory')->get($file->getFileUri());
    if ($image->isValid()) {
      $variables['width'] = $image->getWidth();
      $variables['height'] = $image->getHeight();
    }
    else {
      $variables['width'] = $variables['height'] = NULL;
    }

    return [
      '#theme' => 'image_style',
      '#width' => $variables['width'],
      '#height' => $variables['height'],
      '#style_name' => $variables['style_name'],
      '#uri' => $variables['uri'],
    ];
  }

  /**
   * Gets a slide show image from a gift type node.
   *
   * Since we reference to our custom entity type editorial content which
   * has a reference to another entity type field collection which also
   * has a reference to a file we need a lot of work to get the image fid.
   *
   * @param $node
   *   The gift_type node to get the slide show image from.
   * @return bool|\Drupal\file\Entity\File
   *  The file entity if found otherwise FALSE.
   */
  public static function getSlideshowSlideImage(NodeInterface $node) {
    if ($node->bundle() != 'gift_type') {
      return FALSE;
    }

    if (!$node->field_slideshow_slides->isEmpty()) {
      $slideshow = \Drupal::entityTypeManager()
        ->getStorage('editorial_content')
        ->load($node->field_slideshow_slides[0]->target_id);
      if ($slideshow !== NULL && !$slideshow->field_slideshow_slide->isEmpty()) {
        $slide = \Drupal::entityTypeManager()
          ->getStorage('field_collection_item')
          ->load($slideshow->field_slideshow_slide[0]->value);

        if ($slide !== NULL && !$slide->field_slideshow_image->isEmpty()) {
          return File::load($slide->field_slideshow_image[0]->target_id);
        }
      }
    }

    return FALSE;
  }
}
