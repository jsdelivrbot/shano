<?php

namespace Drupal\giftshop\Plugin\Field\FieldWidget;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\BeforeCommand;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\RemoveCommand;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\giftshop\GiftshopCartInterface;
use Drupal\giftshop\GiftshopCartItemInterface;
use Drupal\node\Entity\Node;

/**
 * Plugin implementation of the 'wovi_giftshop_widget' widget.
 *
 * @FieldWidget(
 *   id = "wovi_giftshop_widget",
 *   multiple_values = TRUE,
 *   label = @Translation("wovi giftshop widget"),
 *   field_types = {
 *     "wovi_giftshop_field"
 *   }
 * )
 */
class WoViGiftshopWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element = [];

    // This throws fatals in CLI context & drops queue processing as user is not available & storage fails.
    if (PHP_SAPI !== 'cli') {
      /** @var GiftshopCartInterface $cart_service */
      $cart_service = \Drupal::service('giftshop.cart');
      $cart_items = $cart_service->getItems();

      $gifts = [];

      foreach ($cart_items as $cart_index => $cart_item) {
        /** @var GiftshopCartItemInterface $cart_item */
        if ($node = Node::load($cart_item->getNodeId())) {
          $gift_id = $node->get('field_gift_id')->value;
          $gifts[] = [
            'node_id' => $cart_item->getNodeId(),
            'quantity' => $cart_item->getQuantity(),
            'gift_id' => $gift_id,
            'response_type' => $cart_item->getResponseType(),
            'response_data' => $cart_item->getResponseData(),
          ];

        }
      }
      $amount = $cart_service->getTotalPrice();

      $element['amount'] = [
        '#type' => 'hidden',
        '#value' => $amount,
      ];

      $element['gift_price'] = [
        '#type' => 'hidden',
        '#value' => $amount,
      ];

      $element['gifts'] = [
        '#type' => 'hidden',
        '#value' => serialize($gifts),
      ];


      if (!empty($this->getFieldSetting('headline'))) {
        $element['headline'] = [
          '#markup' => $this->getFieldSetting('headline'),
        ];
      }
      if (!empty($this->getFieldSetting('subline'))) {
        $element['subline'] = [
          '#markup' => $this->getFieldSetting('subline'),
        ];
      }
    }

    return $element;
  }

  public function form(FieldItemListInterface $items, array &$form, FormStateInterface $form_state, $get_delta = NULL) {
    $form_parent = parent::form($items, $form, $form_state, $get_delta);
    $form_parent['widget']['#theme'] = 'form_section__wovi_giftshop';
    $form_parent['widget']['#elements'] = $form_parent['widget'];

    return $form_parent;
  }


  /**
   * Form validation handler for widget elements.
   *
   * @param array $element
   *   The form element.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   */
  public static function validateElement(array $element, FormStateInterface &$form_state) {

    $form_state->setLimitValidationErrors(NULL);
    $trigger_element = $form_state->getTriggeringElement();

    $error = $form_state->getError($trigger_element);
    if ($error !== NULL) {
      $form_state->setRebuild(TRUE);
    }
    else {
      $form_state->setRebuild(FALSE);
    }

  }

  /**
   * Ajax callback to validate the donation form.
   */
  public static function validateFormAjax(array &$form, FormStateInterface $form_state) {
    $trigger_element = $form_state->getTriggeringElement();
    $trigger_input_id = $trigger_element['#attributes']['data-drupal-selector'];

    $response = new AjaxResponse();

    $error = $form_state->getError($trigger_element);
    if ($error !== NULL) {
      $error_render_array = [
        '#theme' => 'status_messages',
        '#message_list' => array('error' => array($error)),
        '#status_headings' => [
          'status' => t('Status message'),
          'error' => t('Error message'),
          'warning' => t('Warning message'),
        ],
      ];

      $response->addCommand(new RemoveCommand('#error-message-' . $trigger_input_id));
      $response->addCommand(new BeforeCommand('#' . $trigger_input_id, '<div class="error-message" id="error-message-' . $trigger_input_id . '"></div>'));
      $response->addCommand(new HtmlCommand('#error-message-' . $trigger_input_id, $error_render_array));
    }
    else {
      $response->addCommand(new RemoveCommand('#error-message-' . $trigger_input_id));
    }

    return $response;
  }

}
