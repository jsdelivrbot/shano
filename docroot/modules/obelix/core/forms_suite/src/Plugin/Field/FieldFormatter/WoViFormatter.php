<?php

namespace Drupal\forms_suite\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'wovi_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "wovi_formatter",
 *   label = @Translation("Forms suite formatter"),
 *   field_types = {
 *     "wovi_child_field",
 *     "wovi_child_select_field",
 *     "wovi_company_cooperation_field",
 *     "wovi_donation_amount_field",
 *     "wovi_donation_period_field",
 *     "wovi_donation_receipt_field",
 *     "wovi_message_field",
 *     "wovi_newsletter_field",
 *     "wovi_press_distributor_field",
 *     "wovi_suggestion_field",
 *     "wovi_user_data_email_field",
 *     "wovi_user_data_field",
 *     "wovi_payment_method_field",
 *     "wovi_yearly_donation_field",
 *   }
 * )
 */
class WoViFormatter extends FormatterBase {
  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return array(// Implement default settings.
      ) + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    return array(// Implement settings form.
      ) + parent::settingsForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    // Implement settings summary.

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $delta => $item) {

      $rows = [];
      foreach ($item->getValue() as $key => $value) {
        if (!is_array($value) || $key == 'donationReceipt') {
          if(is_array($value)){
            $rows[] = [
              0 => $key,
              1 => var_export($value,true),
            ];
          }else {
            $rows[] = [
              0 => $key,
              1 => $value,
            ];
          }
        }
      }

      $table = [
        '#type' => 'table',
        '#header' => ['key', 'value'],
        '#rows' => $rows,
        '#empty' => $this->t('No data attached.'),
      ];

      $container = [
        '#type' => 'fieldset',
        '#markup' => \Drupal::service('renderer')->render($table),
      ];

      $elements[] = $container;

    }


    return $elements;
  }

  /**
   * Generate the output appropriate for one field item.
   *
   * @param \Drupal\Core\Field\FieldItemInterface $item
   *   One field item.
   *
   * @return string
   *   The textual output generated.
   */
  protected function viewValue(FieldItemInterface $item) {
    // The text value has no text format assigned to it, so the user input
    // should equal the output, including newlines.
    return nl2br(Html::escape($item->value));
  }

}
