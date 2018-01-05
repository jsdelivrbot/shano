<?php

namespace Drupal\wv_simma_connector\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Extension;
use Drupal\Core\Url;

/**
 * General site administration form.
 */
class ConfigForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'wv_simma_connector_config_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'wv_simma_connector.settings',
      'wv_simma_connector.settings.migrate',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('wv_simma_connector.settings');
    $migrate_config = $this->config('wv_simma_connector.settings.migrate');

    $export = (array) $config->get('export');

    $form['simma_connector'] = array(
      '#type' => 'vertical_tabs',
      '#title' => t('Simma Connector Settings'),
    );

    $form['migrate'] = array(
      '#type' => 'details',
      '#title' => t('Migrate'),
      '#group' => 'simma_connector',
    );

    $form['migrate']['media_endpoint'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Media endpoint'),
      '#description' => $this->t('Enter full path e.g. https://endpoint.com:443/media/api.'),
      '#default_value' => $migrate_config->get('media_endpoint'),
    );

    $form['migrate']['media_endpoint_user'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Media endpoint user'),
      '#description' => $this->t('Basic auth.'),
      '#default_value' => $migrate_config->get('media_endpoint_user'),
    );

    $form['migrate']['media_endpoint_pass'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Media endpoint password'),
      '#description' => $this->t('Basic auth.'),
      '#default_value' => $migrate_config->get('media_endpoint_pass'),
    );

    $form['export'] = array(
      '#type' => 'details',
      '#title' => t('Export'),
      '#group' => 'simma_connector',
    );

    $form['export']['allowed_field_types'] = array(
      '#type' => 'textarea',
      '#title' => $this->t('Allowed field types'),
      '#description' => $this->t(
        'All these types will be exported, it is real D8 types machine names. Delimeter is comma.'
      ),
      '#default_value' => !empty($export['allowed_field_types'])
        ? $export['allowed_field_types'] : '',
    );

    $form['export']['allowed_properties'] = array(
      '#type' => 'textarea',
      '#title' => $this->t('Allowed properties'),
      '#description' => $this->t(
        'All these properties will be exported, it is real D8 properties machine names. Delimeter is comma.'
         . ' Please note that field type of allowed property should be added too, e.g. for `created` property it is'
         . ' `created` and not integer, so please be careful and check field type to be sure it has correct name.'
      ),
      '#default_value' => !empty($export['allowed_properties'])
        ? $export['allowed_properties'] : '',
    );

    $form['export']['excluded_fields'] = array(
      '#type' => 'textarea',
      '#title' => $this->t('Excluded fields'),
      '#description' => $this->t(
        'All these field will be excluded, it is real D8 fields machine names. Delimeter is comma.'
      ),
      '#default_value' => !empty($export['excluded_fields'])
        ? $export['excluded_fields'] : '',
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {}

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $export = [
      'allowed_field_types' => $form_state->getValue('allowed_field_types'),
      'allowed_properties' => $form_state->getValue('allowed_properties'),
      'excluded_fields' => $form_state->getValue('excluded_fields'),
    ];

    // Retrieve the configuration.
    $this->config('wv_simma_connector.settings')
      // Set the submitted configuration setting.
      ->set('export', $export)
      ->save();

    $this->config('wv_simma_connector.settings.migrate')
      ->set('media_endpoint', $form_state->getValue('media_endpoint'))
      ->set('media_endpoint_user', $form_state->getValue('media_endpoint_user'))
      ->set('media_endpoint_pass', $form_state->getValue('media_endpoint_pass'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
