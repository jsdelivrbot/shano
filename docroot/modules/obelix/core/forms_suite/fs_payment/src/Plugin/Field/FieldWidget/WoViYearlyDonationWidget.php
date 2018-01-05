<?php

namespace Drupal\fs_payment\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\forms_suite\Plugin\Field\FieldWidget\FormsSuiteWidget;

/**
 * Plugin implementation of the 'wovi_yearly_donation_widget' widget.
 *
 * @FieldWidget(
 *   id = "wovi_yearly_donation_widget",
 *   multiple_values = TRUE,
 *   label = @Translation("Forms suite yearly donation widget"),
 *   field_types = {
 *     "wovi_yearly_donation_field"
 *   }
 * )
 */
class WoViYearlyDonationWidget extends FormsSuiteWidget
{
  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state)
  {
    $element = parent::formElement($items, $delta, $element, $form, $form_state);

    $element['month13'] = [
      '#type' => 'number',
      '#default_value' => isset($items[$delta]->value) ? $items[$delta]->value : 30,
      '#ajax' => [
        'callback' => [$this, "validateFormAjax"],
        'event' => 'blur',
        'disable-refocus' => TRUE,
        'progress' => [
          'type' => 'throbber',
          'message' => t('Validating'),
        ],
      ],
      '#element_validate' => [[get_class($this), 'validateElement']],
    ];

    $month13_check_options = [
      0 => $this->t('No'),
      1 => $this->t('Yes, amount') . " (€):",
    ];
    if (forms_suite_is_child_sponsorship_english_form()) {
      $month13_check_options = [
        0 => 'No',
        1 => 'Yes, amount (€):',
      ];
    }
    $element['month13_check'] = [
      '#type' => 'radios',
      '#options' => $month13_check_options,
      '#default_value' => 0,
      '#title' => t(''),
    ];

    return $element;
  }


  /**
   * {@inheritdoc}
   */
  public static function validateElement(array $element, FormStateInterface &$form_state)
  {

    $form_state->setLimitValidationErrors(NULL);
    $section_values = $form_state->getValues();
    $field_yearly_donation = $section_values['field_yearly_donation'];

    switch (end($element['#parents'])) {
      case "month13" :
        if ($element['#value'] < 5) {
          $form_state->setError($element, t('@name have to be at least @min€.', array(
            '@name' => $element['#title'],
            '@min' => 5,
          )));
        } elseif (!$field_yearly_donation['month13_check']) {
          // if checkbox is not activated delete default value.
          $field_yearly_donation['month13'] = '';
          $form_state->setValue('field_yearly_donation', $field_yearly_donation);
        }
        break;
    }

    parent::validateElement($element, $form_state);
  }

}
