<?php

namespace Drupal\forms_suite\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'wovi_press_distributor_widget' widget.
 *
 * @FieldWidget(
 *   id = "wovi_press_distributor_widget",
 *   multiple_values = TRUE,
 *   label = @Translation("Forms suite press distributor widget"),
 *   field_types = {
 *     "wovi_press_distributor_field"
 *   }
 * )
 */
class WoViPressDistributorWidget extends FormsSuiteWidget
{
  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state)
  {

    $element = parent::formElement($items, $delta, $element, $form, $form_state);

    $element['adressnumber'] = [
      '#type' => 'textfield',
      '#title' => t('Adressnumber'),
      '#element_validate' => [[get_class($this), 'validateElement']],
    ];

    $element['editorialStaff'] = [
      '#type' => 'textfield',
      '#title' => t('Editorial staff'),
      '#element_validate' => [[get_class($this), 'validateElement']],
    ];

    $element['journalist'] = [
      '#type' => 'radios',
      '#title' => t('Journalist / media representatives'),
      '#description' => t('Journalist / media representatives'),
      '#options' => [
        0 => t('no'),
        1 => t('yes'),
      ],
      '#element_validate' => [[get_class($this), 'validateElement']],
      '#default_value' => 0
    ];

    $element['shippingMethod'] = [
      '#type' => 'radios',
      '#title' => t('Preferred shipping method'),
      '#description' => t('Preferred shipping method'),
      '#options' => [
        0 => t('E-Mail'),
        1 => t('Fax'),
      ],
      '#element_validate' => [[get_class($this), 'validateElement']],
      '#default_value' => 0
    ];

    $element['topics'] = [
      '#type' => 'radios',
      '#title' => t('Are there topics you particularly interested in?'),
      '#options' => [
        0 => t('Long-term development cooperation'),
        1 => t('Humanitarian assistance / disaster relief'),
        2 => t('Campaigns and lobbying'),
      ],
      '#element_validate' => [[get_class($this), 'validateElement']],
    ];

    $element['printed'] = [
      '#type' => 'radios',
      '#title' => t('Are you interested in the printed publications of World Vision?'),
      '#options' => [
        0 => t('How can journalists help'),
        1 => t('annual report'),
        2 => t('Global Future ( 4 times a year / English )'),
      ],
      '#element_validate' => [[get_class($this), 'validateElement']],
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
