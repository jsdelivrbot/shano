<?php

namespace Drupal\forms_suite\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'wovi_user_data_email_widget' widget.
 *
 * @FieldWidget(
 *   id = "wovi_user_data_email_widget",
 *   multiple_values = TRUE,
 *   label = @Translation("Forms suite user data email widget"),
 *   field_types = {
 *     "wovi_user_data_email_field"
 *   }
 * )
 */
class WoViUserDataEmailWidget extends FormsSuiteWidget
{
  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state)
  {
    $element = parent::formElement($items, $delta, $element, $form, $form_state);

    $request = \Drupal::request()->request;

    $element['salutation'] = [
      '#type' => 'select',
      '#title' => t('Salutation'),
      '#default_value' => isset($items[$delta]->value) ? $items[$delta]->value : 1,
      '#options' => [
        1 => t('Mr.'),
        2 => t('Mrs.'),
        3 => t('Family'),
        4 => t('Mr. and Mrs.'),
        100 => t('Company'),
      ],
      '#element_validate' => [[get_class($this), 'validateElement']],
    ];

    $element['firstName'] = [
      '#type' => 'textfield',
      '#title' => t('First name'),
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
      '#required' => TRUE,
      '#maxlength' => 30,
    ];

    $element['surname'] = [
      '#type' => 'textfield',
      '#title' => t('Surname'),
      '#ajax' => [
        'callback' => [get_class($this), 'validateFormAjax'],
        'event' => 'blur',
        'disable-refocus' => TRUE,
        'progress' => [
          'type' => 'throbber',
          'message' => t('Validating'),
        ],
      ],
      '#element_validate' => [[get_class($this), 'validateElement']],
      '#required' => TRUE,
      '#maxlength' => 30,
    ];

    $element['phonePrivate'] = [
      '#type' => 'textfield',
      '#maxlength' => $this->getFieldSetting('max_length'),
      '#title' => t('Phone private (optional, for any questions)'),
      '#maxlength' => 30,
    ];

    $element['email'] = [
      '#type' => 'email',
      '#title' => t('E-Mail'),
      '#ajax' => [
        'callback' => [get_class($this), 'validateFormAjax'],
        'event' => 'blur',
        'disable-refocus' => TRUE,
        'progress' => [
          'type' => 'throbber',
          'message' => t('validating'),
        ],
      ],
      '#element_validate' => [[get_class($this), 'validateElement']],
      '#required' => TRUE,
      '#maxlength' => 80,
    ];

    return $element;
  }


  /**
   * {@inheritdoc}
   */
  public static function validateElement(array $element, FormStateInterface &$form_state)
  {

    $form_state->setLimitValidationErrors(NULL);

    if ($element['#required'] && $element['#value'] == '') {
      $form_state->setError($element, t('@name field is required.', array('@name' => $element['#title'])));
    }

    switch (end($element['#parents'])) {
      case "email" :
        if (!\Drupal::service('email.validator')->isValid($element['#value'])) {
          $form_state->setError($element, t('The e-mail is not a valid.'));
        }
        break;
    }

    parent::validateElement($element, $form_state);
  }

}
