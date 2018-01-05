<?php

namespace Drupal\forms_suite\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'wovi_donation_receipt_widget' widget.
 *
 * @FieldWidget(
 *   id = "wovi_donation_receipt_widget",
 *   multiple_values = TRUE,
 *   label = @Translation("Forms suite donation receipt widget"),
 *   field_types = {
 *     "wovi_donation_receipt_field"
 *   }
 * )
 */
class WoViDonationReceipt extends FormsSuiteWidget
{
  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state)
  {

    $element = parent::formElement($items, $delta, $element, $form, $form_state);

    $block_time = \Drupal::service('date.formatter')
      ->format(REQUEST_TIME ,'custom', 'Y');

    $options = [];
    for ($i = 5; $i> 0; $i--){
      $year = $block_time - $i;
      $options[$year] = $year;
    }

    $element['#attached']['library'][] = 'beaufix/bootstrap_select';

    $element['donationReceipt'] = [
        '#type' => 'select',
      '#title' => t('year'),
      '#options' => $options,
      '#multiple' => TRUE,
      '#element_validate' => [[get_class($this), 'validateElement']],
      '#attributes' => [
        'class' => ['selectpicker'],
        'title' => [t('No year selected.')],
      ],
      '#required' => TRUE,
    ];

    $element['adressnumber'] = [
      '#type' => 'textfield',
      '#title' => t('Adressnumber'),
      '#element_validate' => [[get_class($this), 'validateElement']],
      '#required' => TRUE,
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
