<?php

namespace Drupal\fs_payment\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\forms_suite\Plugin\Field\FieldWidget\FormsSuiteWidget;

/**
 * Plugin implementation of the 'wovi_donation_amount_widget' widget.
 *
 * @FieldWidget(
 *   id = "wovi_donation_amount_widget",
 *   multiple_values = TRUE,
 *   label = @Translation("Forms suite donation amount widget"),
 *   field_types = {
 *     "wovi_donation_amount_field"
 *   }
 * )
 */
class WoViDonationAmountWidget extends FormsSuiteWidget
{
  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state)
  {
    $element = parent::formElement($items, $delta, $element, $form, $form_state);

    $forms_suite_configs = $this->getFieldSetting('forms_suite_configs');

    if (!empty($forms_suite_configs['minimal_amount'])) {
      $minimal_amount = $forms_suite_configs['minimal_amount'];
    } else {
      $minimal_amount = NULL;
    }

    if (!empty($forms_suite_configs['default_amount'])) {
      $default_amount = $forms_suite_configs['default_amount'];
    } else {
      $default_amount = NULL;
    }

    $single_amount_prefix = '';
    $single_amount_suffix = ' €';
    if (forms_suite_is_child_sponsorship_english_form()) {
      $single_amount_prefix = '€ ';
      $single_amount_suffix = '';
    }

    if (!$forms_suite_configs['single_amount']) {
      $element['amountRadio'] = [
        '#type' => 'radios',
        '#title' => t('Select amount'),
        '#options' => [
          30 => $single_amount_prefix . 30 . $single_amount_suffix,
          40 => $single_amount_prefix . 40 . $single_amount_suffix,
          50 => $single_amount_prefix . 50 . $single_amount_suffix,
        ],
        '#element_validate' => [[get_class($this), 'validateElement']],
        '#default_value' => isset($items[$delta]->value) ? $items[$delta]->value : 30,
      ];
    } else {
      $element['amountRadio'] = [
        '#type' => 'hidden',
        '#value' => 0,
        '#element_validate' => [[get_class($this), 'validateElement']],
        '#ajax' => [
          'callback' => [$this, "validateFormAjax"],
          'event' => 'blur',
          'disable-refocus' => TRUE,
          'progress' => [
            'type' => 'throbber',
            'message' => t('validating'),
          ],
        ],
      ];
    }

    $amount_title = t('Amount') . ' (€)';
    if (forms_suite_is_child_sponsorship_english_form()) {
      $amount_title = 'Amount (€)';
    }
    $element['amount'] = [
      '#type' => 'number',
      '#title' => $amount_title,
      '#default_value' => $default_amount,
      '#min' => $minimal_amount,
      '#ajax' => [
        'callback' => [$this, "validateFormAjax"],
        'event' => 'blur',
        'disable-refocus' => TRUE,
        'progress' => [
          'type' => 'throbber',
          'message' => t('validating'),
        ],
      ],
      '#element_validate' => [[get_class($this), 'validateElement']],
    ];


    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function form(FieldItemListInterface $items, array &$form, FormStateInterface $form_state, $get_delta = NULL)
  {
    $forms_suite_configs = $this->getFieldSetting('forms_suite_configs');

    if ($forms_suite_configs['single_amount']) {
      $theme = 'form_section__wovi_donation_amount_single';
    } else {
      $theme = 'form_section__wovi_donation_amount';
    }

    $form_parent = parent::form($items, $form, $form_state, $get_delta);
    $form_parent['widget']['#theme'] = $theme;
    $form_parent['widget']['#cache']['max-age'] = 0;
    $form_parent['widget']['#elements'] = $form_parent['widget'];

    return $form_parent;
  }

  /**
   * {@inheritdoc}
   */
  public static function validateElement(array $element, FormStateInterface &$form_state)
  {

    $form_state->setLimitValidationErrors(NULL);
    $section_values = $form_state->getValues();
    switch (end($element['#parents'])) {
      case "amount" :
        if ($element['#value'] < $element['#min'] && $section_values['field_donation_amount']['amountRadio'] == 0) {
          $form_state->setError($element,
            t('@name have to be at least @min.', array('@name' => $element['#title'], '@min' => $element['#min'])));
        }
        break;
      case 'amountRadio':
        if ($element['#value'] != 0 && $element['#value'] !== NULL) {
          $section_values['field_donation_amount']['amount'] = $element['#value'];
          $form_state->setValue('field_donation_amount', $section_values['field_donation_amount']);
        }
        break;
    }
    parent::validateElement($element, $form_state);
  }

}
