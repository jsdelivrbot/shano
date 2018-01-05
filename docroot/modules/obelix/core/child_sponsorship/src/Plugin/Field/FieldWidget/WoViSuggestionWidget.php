<?php

namespace Drupal\child_sponsorship\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\forms_suite\Plugin\Field\FieldWidget\FormsSuiteWidget;

/**
 * Plugin implementation of the 'wovi_suggestion_widget' widget.
 *
 * @FieldWidget(
 *   id = "wovi_suggestion_widget",
 *   multiple_values = TRUE,
 *   label = @Translation("Forms suite suggestion widget"),
 *   field_types = {
 *     "wovi_suggestion_field"
 *   }
 * )
 */
class WoViSuggestionWidget extends FormsSuiteWidget
{

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state)
  {
    $element = parent::formElement($items, $delta, $element, $form, $form_state);

    $forms_suite_configs = $this->getFieldSetting('forms_suite_configs');

    $element['suggestion'] = [
      '#type' => 'radios',
      '#title' => t('Suggestion'),
      '#default_value' => isset($forms_suite_configs['suggestion_type']) ? $forms_suite_configs['suggestion_type'] : 1,
      '#options' => [
        1 => t('Send me a sponsorship suggestion via e-mail'),
        2 => t('Send me a sponsorship suggestion via post'),
      ],
      '#attributes' => [
        'class' => [
          'hidden',
        ],
      ],
      '#element_validate' => [
        [get_class($this), 'validateElement']
      ],
    ];


    $element['childSequenceNo'] = [
      '#type' => 'hidden',
      '#value' => '',
    ];
    $element['childCountryCode'] = [
      '#type' => 'hidden',
      '#value' => '',
    ];


    if (!empty($forms_suite_configs['suggestion_type'])) {
      switch ($forms_suite_configs['suggestion_type']) {
        case 1:
          $element['suggestion_type']['email'] = [
            '#markup' => 'active',
          ];
          break;
        case 2:
          $element['suggestion_type']['post'] = [
            '#markup' => 'active',
          ];
          break;
      }
    }

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public static function validateElement(array $element, FormStateInterface &$form_state)
  {
    // validate your fields.
    parent::validateElement($element, $form_state);
  }

}
