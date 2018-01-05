<?php

namespace Drupal\forms_suite\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'wovi_newsletter_widget' widget.
 *
 * @FieldWidget(
 *   id = "wovi_newsletter_widget",
 *   multiple_values = TRUE,
 *   label = @Translation("Forms suite newsletter widget"),
 *   field_types = {
 *     "wovi_newsletter_field"
 *   }
 * )
 */
class WoViNewsletterWidget extends FormsSuiteWidget
{
  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state)
  {
    $element = parent::formElement($items, $delta, $element, $form, $form_state);

    $newsletter_title = t('I want to receive the World Vision newsletter.');
    if (forms_suite_is_child_sponsorship_english_form()) {
      $newsletter_title = 'I would like to receive the World Vision Newsletter.';
    }
    $element['newsletter'] = [
      '#type' => 'checkbox',
      '#title' => $newsletter_title,
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
