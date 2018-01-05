<?php

/**
 * @file
 * Contains Drupal\editorial\Form\EditorialFieldConfigurationForm.
 */

namespace Drupal\editorial\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\editorial\EditorialFieldGlobalConfigurationManager;

/**
 * Class EditorialFieldGlobalConfigurationForm.
 *
 * @package Drupal\editorial\Form
 */
class EditorialFieldGlobalConfigurationForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'editorial.editorial_field.global_configuration',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'editorial_field_global_configuration_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('editorial.editorial_field.global_configuration');

    $form['layouts'] = [
      '#type' => 'details',
      '#title' => t('Available Layouts'),
      '#description' => t('Select which layouts should be available for editorial fields, you can confine this selection for each field separately. No selection means all are available.'),
      '#open' => !empty($config->get('layouts')),
      '#tree' => TRUE,
    ];

    $form['layouts']['definitions'] = [
      '#type' => 'checkboxes',
      '#options' => EditorialFieldGlobalConfigurationManager::getLayoutOptions(),
      '#default_value' => $config->get('layouts'),
    ];

    $form['entities'] = [
      '#type' => 'details',
      '#title' => 'Available content',
      '#description' => t('Select which content types should be available for editorial fields, you can confine this selection for each field separately. No selection means all are available.'),
      '#open' => !empty($config->get('entities')),
      '#tree' => TRUE,
    ];

    $entities = EditorialFieldGlobalConfigurationManager::getEntityOptions();
    $config_entities = $config->get('entities');

    foreach ($entities as $entity_type_id => $entity_label) {
      $form['entities'][$entity_type_id] =  [
        '#type' => 'checkboxes',
        '#title' => $entity_label,
        '#options' => EditorialFieldGlobalConfigurationManager::getEntityBundleOptions($entity_type_id),
        '#default_value' => isset($config_entities[$entity_type_id]) ? $config_entities[$entity_type_id] : [],
      ];
    }

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('editorial.editorial_field.global_configuration')
      ->set('layouts', array_filter($form_state->getValue(['layouts', 'definitions'], [])))
      ->set('entities', array_filter($form_state->getValue('entities', [])))
      ->save();

    parent::submitForm($form, $form_state);
  }
}
