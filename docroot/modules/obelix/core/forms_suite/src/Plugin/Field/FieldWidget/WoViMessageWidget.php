<?php

namespace Drupal\forms_suite\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'wovi_message_widget' widget.
 *
 * @FieldWidget(
 *   id = "wovi_message_widget",
 *   multiple_values = TRUE,
 *   label = @Translation("Forms suite message widget"),
 *   field_types = {
 *     "wovi_message_field"
 *   }
 * )
 */
class WoViMessageWidget extends FormsSuiteWidget
{
  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state)
  {
    $element = parent::formElement($items, $delta, $element, $form, $form_state);


    $message_title = t('Your message for World Vision…');
    if (forms_suite_is_child_sponsorship_english_form()) {
      $message_title = 'Your message to World Vision';
    }
    $element['message'] = [
      '#type' => 'textarea',
      '#title' => $message_title,
      '#element_validate' => [[get_class($this), 'validateElement']],
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public static function validateElement(array $element, FormStateInterface &$form_state){
    // validate your fields.
    parent::validateElement($element, $form_state);
  }

}
