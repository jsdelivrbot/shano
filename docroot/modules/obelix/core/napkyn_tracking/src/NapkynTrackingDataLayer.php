<?php
/**
 * User: MoeVoe
 * Date: 08.12.16
 * Time: 16:42
 */

namespace Drupal\napkyn_tracking;

use Drupal\child\Controller\ChildController;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Field\EntityReferenceFieldItemList;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;
use Drupal\editorial\Plugin\Search\NodeSearch;
use Drupal\editorial_content\Entity\EditorialContent;
use Drupal\forms_suite\Entity\Form;
use Drupal\forms_suite\Event\FormEvents;
use Drupal\forms_suite\Event\FormSubmitAjaxEvent;
use Drupal\giftshop\GiftshopCartInterface;
use Drupal\giftshop\GiftshopCartItemInterface;
use Drupal\google_tag_manager\DataLayer;
use Drupal\node\Entity\Node;
use Drupal\search\Entity\SearchPage;
use Symfony\Component\EventDispatcher\Event;

class NapkynTrackingDataLayer extends DataLayer {

  /**
   * @inheritdoc
   */
  public function buildGTMDataLayer(Event $event) {

    // home page
    $tracking_data[] = [
      'url' => Url::fromUserInput('/'),
      'data' => [
        'page_name' => 'homepage',
        'page_type' => 'homepage',
      ],
    ];

    $tracking_data[] = [
      'url' => Url::fromUserInput('/home'),
      'data' => [
        'page_name' => 'homepage',
        'page_type' => 'homepage',
      ],
    ];

    // Child sponsorship content page
    $tracking_data[] = [
      'url' => Url::fromUserInput('/kinderpatenschaft/informieren'),
      'data' => [
        'page_name' => 'Child sponsorship',
        'page_type' => 'content',
      ],
    ];

    // Cart page
    $tracking_data[] = [
      'url' => Url::fromUserInput('/giftshop/cart'),
      'data' => [
        'page_name' => 'Basket',
        'page_type' => 'cart',
      ],
    ];

    // Previously the child data forced a session cookie which prevented
    // any possibility of caching any page or serving any page from cache
    // once a single child is requested for a user. For now, we are
    // preventing this data from being used on all pages per task:
    // WV-16: Remove PHP Session usage.
    // Unfortunately, sessions are used a lot, and there are far too many
    // places where cache is simply disabled. Additionally the components
    // are very tightly coupled, so it is difficult to remove sessions and
    // actually the whole caching strategy should be redesigned from scratch
    // in order to optimize the site performance.
    // @TODO - Refactor tracking so it does not needlessly request dynamic
    // data in order to improve caching and performance.
    $child_controller = new ChildController();
    $current_path = \Drupal::service('path.current')->getPath();
    $current_url = Url::fromUserInput($current_path)->toString();

    if ($current_url === '/kinderpatenschaft' && $child = $child_controller->getChildFromUser()) {
      // Child sponsorship detail page
      $tracking_data[] = [
        'url' => Url::fromUserInput('/kinderpatenschaft'),
        'data' => [
          'page_name' => 'Kinderpatenschaft',
          'page_type' => 'child sponsorship page',
          'ecommerce' => [
            'detail' => [
              'products' => [
                'name' => 'Child sponsorship',
                'id' => $child->getIVisionID(),
                'category' => 'Sponsorship/' . $child->getCountry()->label(),
              ]
            ]
          ]
        ],
      ];

      // events
      $tracking_data[] = [
        'url' => Url::fromUserInput('/kinderpatenschaft'),
        'type' => 'event',
        'selector' => '.editorial-child-sponsorship-child-select a.btn-beauty',
        'data' => [
          'event' => 'addToCart',
          'ecommerce' => [
            'add' => [
              'products' => [
                'name' => 'Child Sponsorship',
                'id' => $child->getIVisionID(),
                // 'price' => '',
                // 'category' => 'Gifts',
                'quantity' => 1,
              ]
            ]
          ]
        ],
      ];
    }

    if ($current_url === '/forms/child_sponsorship_with_condition' && $child = $child_controller->getChildFromUser()) {
      // Child Sponsorship Checkout page
      $tracking_data[] = [
        'url' => Url::fromUserInput('/forms/child_sponsorship_with_condition'),
        'data' => [
          'page_name' => 'Kinderpatenschaft uebernehmen',
          'page_type' => 'checkout page',
          'ecommerce' => [
            'checkout' => [
              'actionField' => [
                'step' => 1,
              ],
              'products' => [
                'name' => 'Child sponsorship',
                'id' => $child->getIVisionID(),
                'category' => 'Sponsorship/' . $child->getCountry()->label(),
              ]
            ]
          ]
        ],
      ];
    }

    if ($current_url === '/forms/child_sponsorship_direct' && $child = $child_controller->getChildFromUser()) {
      $tracking_data[] = [
        'url' => Url::fromUserInput('/forms/child_sponsorship_direct'),
        'data' => [
          'page_name' => 'Kinderpatenschaft uebernehmen',
          'page_type' => 'checkout page',
          'ecommerce' => [
            'checkout' => [
              'actionField' => [
                'step' => 1,
              ],
              'products' => [
                'name' => 'Child sponsorship',
                'id' => $child->getIVisionID(),
                'category' => 'Sponsorship/' . $child->getCountry()->label(),
              ]
            ]
          ]
        ],
      ];
    }

    // Product list page
    // node list
    $gift_overview_page = [
      623, // Schulspeisung
      608, // Wasserfilter
      619, // Solarlampe
      598, // Winterjacke
      612, // Ziege
      613, // Spargruppen
      596, // Lehrmaterial
      614, // Moskitonetze
      605, // Bienenstock
    ];

    $node_storage = \Drupal::entityTypeManager()->getStorage('node');
    $nodes = $node_storage->loadMultiple($gift_overview_page);
    $gifts_page = [];
    $position = 1;

    /** @var Node $node */
    foreach ($nodes as $node) {
      $gifts_page[] = [
        'name' => $node->label(),
        'id' => $node->get('field_gift_id')->value,
        'price' => $node->get('field_gift_price')->value,
        'category' => 'Gifts',
        'list' => 'Popular Gifts',
        'position' => $position,
      ];

      // events
      $tracking_data[] = [
        'url' => Url::fromUserInput('/node/591'),
        'type' => 'event',
        'selector' => '#editorial-teaser-' . $node->id() . ' a.btn-beauty',
        'data' => [
          'event' => 'productClick',
          'ecommerce' => [
            'click' => [
              'actionField' => [
                'list' => 'Popular Gifts',
              ],
              'products' => [
                'name' => $node->label(),
                'id' => $node->get('field_gift_id')->value,
                'price' => $node->get('field_gift_price')->value,
                'category' => 'Gifts',
                'position' => $position,
              ]
            ]
          ]
        ],
      ];

      $position++;

    }

    $tracking_data[] = [
      'url' => Url::fromUserInput('/node/591'),
      'data' => [
        'page_name' => 'Das Gute Geschenk',
        'page_type' => 'product list',
        'ecommerce' => [
          'impressions' => $gifts_page,
        ]
      ],
    ];

    /** @var Node $node */
    if ($node = \Drupal::routeMatch()->getParameter('node')) {

      if (!is_object($node)) {
        $node = Node::load($node);
      }
      if ($node->hasField('field_teaser')) {

        /** @var EntityReferenceFieldItemList $teaser */
        $teaser = $node->get('field_teaser');
        $impressions = [];
        $position = 0; // @todo don't know if list is sorted by position.
        /** @var EditorialContent $item */
        foreach ($teaser->referencedEntities() as $item) {
          /** @var EntityReferenceFieldItemList $reference */
          $reference = $item->get('field_entity_reference');
          $reference = $reference->referencedEntities()[0];
          $impressions[] = [
            'name' => $reference->label(),
            'id' => $reference->get('field_gift_id')->value,
            'price' => $reference->get('field_gift_price')->value,
            'category' => 'Gifts',
            'list' => 'Most Popular Gifts',
            'position' => $position++,
          ];
        }

        // gift page
        $tracking_data[] = [
          'options' => [
            'content_type' => 'gift_type'
          ],
          'data' => [
            'page_name' => $node->label(),
            'page_type' => 'product detail',
            'ecommerce' => [
              'detail' => [
                'products' => [
                  'name' => $node->label(),
                  'id' => $node->get('field_gift_id')->value,
                  'price' => $node->get('field_gift_price')->value,
                  'category' => 'Gifts',
                ]
              ]
            ],
            'impressions' => $impressions,
          ],
        ];

        // events
        $tracking_data[] = [
          'options' => [
            'content_type' => 'gift_type'
          ],
          'type' => 'event',
          'selector' => 'form#giftshop-gift-select-form button#edit-submit',
          'data' => [
            'event' => 'addToCart',
            'ecommerce' => [
              'add' => [
                'products' => [
                  'name' => $node->label(),
                  'id' => $node->get('field_gift_id')->value,
                  'price' => $node->get('field_gift_price')->value,
                  'category' => 'Gifts',
                  'quantity' => [
                    'function' => 'val',
                    'selector' => '#edit-quantity',
                  ],
                ],
              ],
            ],
          ],
        ];
      }

      // editorial page
      $tracking_data[] = [
        'options' => [
          'content_type' => 'editorial_page',
          'exception_url' => [
            'url' => [
              Url::fromUserInput('/node/591'),
              Url::fromUserInput('/'),
              Url::fromUserInput('/home'),
            ]
          ],
        ],
        'data' => [
          'page_name' => $node->label(),
          'page_type' => 'content page',
        ],
      ];
    }

    /** @var SearchPage $entity */
    if ($entity = \Drupal::routeMatch()->getParameter('entity')) {

      // search page
      /** @var NodeSearch $plugin */
      if ($plugin = $entity->getPlugin()) {
        $tracking_data[] = [
          'url' => Url::fromUserInput('/search/node'),
          'data' => [
            'page_name' => 'Search Results',
            'page_type' => 'search',
            'search_keyword' => $plugin->getKeywords(),
            'search_results' => $plugin->getResultCount(),
          ],
        ];
      }
    }

    /** @var Form $form */
    if ($form = \Drupal::routeMatch()->getParameter('form')) {

      // lead gen page
      $tracking_data[] = [
        'url' => Url::fromUserInput('/forms/online_spende'),
        'data' => [
          'page_name' => $form->label(),
          'page_type' => 'lead gen page',
        ],
      ];

      // Giftshop checkout page
      /** @var GiftshopCartInterface $cart_service */
      $cart_service = \Drupal::service('giftshop.cart');
      $cart_items = $cart_service->getItems();
      $gifts = [];

      foreach ($cart_items as $cart_index => $cart_item) {
        /** @var GiftshopCartItemInterface $cart_item */
        $variant = $cart_item->getResponseType();
        $gift_data = $cart_item->getResponseData();
        if (isset($gift_data['send_type'])) {
          $variant .= ' / ' . $gift_data['send_type'];
        }
        if ($node = Node::load($cart_item->getNodeId())) {
          $gifts[] = [
            'name' => $node->label(),
            'id' => $node->get('field_gift_id')->value,
            'price' => $node->get('field_gift_price')->value,
            'category' => 'Gifts',
            'variant' => $variant,
            'quantity' => $cart_item->getQuantity(),
          ];
        }
      }

      $tracking_data[] = [
        'url' => Url::fromUserInput('/forms/das_gute_geschenk_checkout'),
        'data' => [
          'page_name' => 'Checkout Step 1',
          'page_type' => 'checkout page',
          'ecommerce' => [
            'checkout' => [
              'actionField' => [
                'step' => 1
              ],
              'products' => $gifts
            ]
          ]
        ],
      ];

      $tracking_data[] = [
        'url' => Url::fromUserInput('/forms/das_gute_geschenk_checkout/send'),
        'data' => [
          'page_name' => 'Thank you',
          'page_type' => 'receipt page',
          'ecommerce' => [
            'checkout' => [
              'actionField' => [
                'id' => 1,
                'affiliation' => 'Germany',
                'revenue' => $cart_service->getTotalPrice(),
                'tax' => 0,
              ],
              'products' => $gifts
            ]
          ]
        ],
      ];

      $tracking_data[] = [
        'url' => Url::fromUserInput('/forms/child_sponsorship_with_condition/send'),
        'data' => [
          'page_name' => 'Kinderpatenschaft uebernehmen',
          'page_type' => 'receipt page',
          'ecommerce' => [
            'checkout' => [
              'actionField' => [
                'affiliation' => 'Germany',
                'tax' => 0,
              ]
            ]
          ]
        ],
      ];

      $tracking_data[] = [
        'url' => Url::fromUserInput('/forms/child_sponsorship_direct/send'),
        'data' => [
          'page_name' => 'Kinderpatenschaft uebernehmen',
          'page_type' => 'receipt page',
          'ecommerce' => [
            'checkout' => [
              'actionField' => [
                'affiliation' => 'Germany',
                'revenue' => 35,
                'tax' => 0,
              ]
            ]
          ]
        ],
      ];

    }

    parent::setData($tracking_data);
  }

  /**
   * @inheritdoc
   */
  public function onFormSubmitSuccess(FormSubmitAjaxEvent $event) {
    $current_path = \Drupal::service('path.current')->getPath();

    if ($current_path == '/forms/online_spende') {
      $event->getAjaxResponse()
        ->addCommand(new InvokeCommand(NULL, "gta_datalayer_event", [
          [
            'event' => 'leadgensubmitted'
          ],
        ]));
    }

    // child sponsorship checkout
    if ($current_path == '/forms/child_sponsorship_with_condition' || $current_path == '/forms/child_sponsorship_direct') {
      $values = $event->getFormState()->getValues();
      $event->getAjaxResponse()
          ->addCommand(new InvokeCommand(NULL, "gta_datalayer_event", [
          [
            'event' => 'checkoutStep',
            'ecommerce' => [
              'checkout' => [
                'actionField' => [
                  'step' => 2,
                ],
                'products' => [
                  'name' => 'Child sponsorship',
                  'id' => $values['field_child']['childSequenceNo'],
                  'price' => $values['field_donation_amount']['amount'],
                  'category' => 'Child Sponsorship',
                  'quantity' => 1,
                ]
              ]
            ]
          ],
        ]));
    }

    // giftshop checkout

    /** @var GiftshopCartInterface $cart_service */
    $cart_service = \Drupal::service('giftshop.cart');
    $cart_items = $cart_service->getItems();
    $gifts = [];

    foreach ($cart_items as $cart_index => $cart_item) {
      /** @var GiftshopCartItemInterface $cart_item */
      $variant = $cart_item->getResponseType();
      $gift_data = unserialize($cart_item->getResponseData());
      if (isset($gift_data['send_type'])) {
        $variant .= ' / ' . $gift_data['send_type'];
      }
      if ($node = Node::load($cart_item->getNodeId())) {
        $gifts[] = [
          'name' => $node->label(),
          'id' => $node->get('field_gift_id')->value,
          'price' => $node->get('field_gift_price')->value,
          'category' => 'Gifts',
          'variant' => $variant,
          'quantity' => $cart_item->getQuantity(),
        ];
      }
    }

    if ($current_path == '/forms/das_gute_geschenk_checkout') {
      $event->getAjaxResponse()
        ->addCommand(new InvokeCommand(NULL, "gta_datalayer_event", [
          [
            'event' => 'checkoutStep',
            'ecommerce' => [
              'checkout' => [
                'actionField' => [
                  'step' => 2,
                ],
                'products' => $gifts
              ]
            ]
          ],
        ]));
    }

    $event->getAjaxResponse()
      ->addCommand(new InvokeCommand(NULL, "gta_datalayer_event", [
        [
          'event' => 'FormSubmission',
          'event_category' => 'Forms',
          'event_action' => 'Form succesfully submitted',
        ],
      ]));

  }


  /**
   * @inheritdoc
   */
  public function onFormSubmitError(FormSubmitAjaxEvent $event) {

    $errors = $event->getFormState()->getErrors();
    $event_label = '';
    foreach ($errors as $error_key => $error) {
      /** @var TranslatableMarkup $error */
      $event_label .= str_replace('][', '/', $error_key) . ', ';
    }
    $event_label = substr($event_label, 0, -2);
    $event->getAjaxResponse()
      ->addCommand(new InvokeCommand(NULL, "gta_datalayer_event", [
        [
          'event' => 'FormSubmission',
          'event_category' => 'Forms',
          'event_action' => 'Form submission errors',
          'event_label' => $event_label,
        ],
      ]));

  }

  /**
   * @inheritdoc
   */
  public static function getSubscribedEvents() {
    if (defined('Drupal\forms_suite\Event\FormEvents::SUBMITTED_SUCCESS')
      && defined('Drupal\forms_suite\Event\FormEvents::SUBMITTED_ERROR')) {

      $events['data_layer.set'][] = 'buildGTMDataLayer';
      $events[FormEvents::SUBMITTED_SUCCESS][] = ['onFormSubmitSuccess', 2048];
      $events[FormEvents::SUBMITTED_ERROR][] = ['onFormSubmitError', 2048];
      return $events;

    }
  }

}
