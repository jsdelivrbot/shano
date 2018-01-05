<?php

namespace Drupal\forms_suite\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\field\Entity\FieldConfig;

abstract class FormsSuiteField extends FieldItemBase
{
  /**
   * {@inheritdoc}
   */
  public static function defaultStorageSettings()
  {
    return array(
        'case_sensitive' => FALSE,
      ) + parent::defaultStorageSettings();
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultFieldSettings()
  {
    $settings = array(
        'forms_suite_configs' => NULL,
      ) + parent::defaultFieldSettings();
    return $settings;
  }

  /**
   * {@inheritdoc}
   */
  public function fieldSettingsForm(array $form, FormStateInterface $form_state)
  {
    $element = parent::fieldSettingsForm($form, $form_state);
    /** @var FieldConfig $field */
    $field = $form_state->getFormObject()->getEntity();

    $forms_suite_configs = $field->getSetting('forms_suite_configs');

    $element['forms_suite_configs'] = [
      '#type' => 'details',
      '#title' => $this->t('Forms suite configs'),
      '#open' => TRUE,
      '#tree' => TRUE,
    ];

    $element['forms_suite_configs']['headline'] = [
      '#type' => 'textfield',
      '#title' => t('Section headline'),
      '#default_value' => $forms_suite_configs['headline'],
      '#description' => t('Write a headline over the form section.'),
    ];

    $element['forms_suite_configs']['subline'] = [
      '#type' => 'textfield',
      '#title' => t('Section subline'),
      '#default_value' => $forms_suite_configs['subline'],
      '#description' => t('Write a subline over the section.'),
    ];

    return $element;
  }

}
