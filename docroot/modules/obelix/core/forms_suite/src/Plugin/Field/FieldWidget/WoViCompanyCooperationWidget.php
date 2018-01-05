<?php

namespace Drupal\forms_suite\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'wovi_company_cooperation_widget' widget.
 *
 * @FieldWidget(
 *   id = "wovi_company_cooperation_widget",
 *   multiple_values = TRUE,
 *   label = @Translation("Forms suite company cooperation widget"),
 *   field_types = {
 *     "wovi_company_cooperation_field"
 *   }
 * )
 */
class WoViCompanyCooperationWidget extends FormsSuiteWidget
{
  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state)
  {
    $element = parent::formElement($items, $delta, $element, $form, $form_state);

    $element['companyCooperationCategory'] = [
      '#type' => 'radios',
      '#title' => t('Company cooperation'),
      '#options' => [
        1 => t('Please call me back'),
        2 => t('Project suggestion'),
        3 => t('Sponsoring / using logo'),
        4 => t('Child sponsorship'),
        5 => t('Disaster relief'),
        6 => t('africa / asia / latin america'),
      ],
      '#element_validate' => [
        [get_class($this), 'validateElement']
      ],
    ];

    $element['companyCooperationSpecial'] = [
      '#type' => 'textfield',
      '#title' => t('Something else'),
      '#element_validate' => [
        [get_class($this), 'validateElement']
      ],
    ];

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
