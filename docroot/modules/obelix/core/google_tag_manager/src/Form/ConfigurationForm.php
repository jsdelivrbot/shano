<?php

namespace Drupal\google_tag_manager\Form;

use Drupal\Core\Config\Config;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class ConfigurationForm.
 *
 * @package Drupal\google_tag_manager\Form
 */
class ConfigurationForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'google_tag_manager.configuration',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'configuration_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('google_tag_manager.configuration');

//    $form['tabs'] = array(
//      '#type' => 'vertical_tabs',
//      '#default_tab' => $default_tab ? $default_tab : 'edit-general',
//      '#attributes' => array('class' => array('google-tag')),
//      '#attached' => array(
//        'css' => array(drupal_get_path('module', 'google_tag') . '/css/google_tag.admin.css'),
//        'js' => array(drupal_get_path('module', 'google_tag') . '/js/google_tag.admin.js'),
//      ),
//    );
//    $form['tabs'] = array(
//      '#type' => 'vertical_tabs',
//      '#default_tab' => $default_tab ? $default_tab : 'edit-general',
//      '#attributes' => array('class' => array('google-tag')),
//      '#attached' => array(
//        'css' => array(drupal_get_path('module', 'google_tag') . '/css/google_tag.admin.css'),
//        'js' => array(drupal_get_path('module', 'google_tag') . '/js/google_tag.admin.js'),
//      ),
//    );

    $form['tabs']['general'] = $this->buildGeneralFieldset($form_state, $config);
    $form['tabs']['paths'] = $this->buildPathFieldset($form_state, $config);
    $form['tabs']['roles'] = $this->buildRoleFieldset($form_state, $config);
    $form['tabs']['statuses'] = $this->buildStatusFieldset($form_state, $config);
    $form['tabs']['advanced'] = $this->buildAdvancedFieldset($form_state, $config);

    return parent::buildForm($form, $form_state);
  }

  public function buildGeneralFieldset(FormStateInterface $form_state, Config $config) {
    $elements = [];

    $elements['container_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Container ID'),
      '#required' => TRUE,
      '#default_value' => $config->get('container_id'),
      '#field_prefix' => 'GTM-',
    ];

    $elements['dev_gtm_auth'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Napkyn DEV gtm_auth param'),
      '#default_value' => $config->get('dev_gtm_auth'),
      '#states' => [
        'visible' => [
          ':input[name="environment"]' => ['value' => 0],
        ],
        'required' => [
          ':input[name="environment"]' => ['value' => 0],
        ],
      ],
    ];

    $elements['dev_gtm_preview'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Napkyn DEV gtm_preview param'),
      '#default_value' => $config->get('dev_gtm_preview'),
      '#states' => [
        'visible' => [
          ':input[name="environment"]' => ['value' => 0],
        ],
        'required' => [
          ':input[name="environment"]' => ['value' => 0],
        ],
      ],
    ];

    $elements['stage_gtm_auth'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Napkyn STAGE gtm_auth param'),
      '#default_value' => $config->get('stage_gtm_auth'),
      '#states' => [
        'visible' => [
          ':input[name="environment"]' => ['value' => 1],
        ],
        'required' => [
          ':input[name="environment"]' => ['value' => 1],
        ],
      ],
    ];

    $elements['stage_gtm_preview'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Napkyn STAGE gtm_preview param'),
      '#default_value' => $config->get('stage_gtm_preview'),
      '#states' => [
        'visible' => [
          ':input[name="environment"]' => ['value' => 1],
        ],
        'required' => [
          ':input[name="environment"]' => ['value' => 1],
        ],
      ],
    ];

    $elements['live_gtm_auth'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Napkyn LIVE gtm_auth param'),
      '#default_value' => $config->get('live_gtm_auth'),
      '#states' => [
        'visible' => [
          ':input[name="environment"]' => ['value' => 2],
        ],
        'required' => [
          ':input[name="environment"]' => ['value' => 2],
        ],
      ],
    ];

    $elements['live_gtm_preview'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Napkyn LIVE gtm_preview param'),
      '#default_value' => $config->get('live_gtm_preview'),
      '#states' => [
        'visible' => [
          ':input[name="environment"]' => ['value' => 2],
        ],
        'required' => [
          ':input[name="environment"]' => ['value' => 2],
        ],
      ],
    ];

    $elements['environment'] = [
      '#type' => 'radios',
      '#title' => $this->t('Environment'),
      '#required' => TRUE,
      '#default_value' => $config->get('environment') == NULL ? 2 : $config->get('environment'),
      '#options' => [
        0 => $this->t('dev'),
        1 => $this->t('stage'),
        2 => $this->t('live'),
      ],

    ];

    return $elements;
  }

  public function buildPathFieldset(FormStateInterface $form_state, Config $config) {
    $elements = [];

    return $elements;
  }

  public function buildRoleFieldset(FormStateInterface $form_state, Config $config) {
    $elements = [];

    return $elements;
  }

  public function buildStatusFieldset(FormStateInterface $form_state, Config $config) {
    $elements = [];

    return $elements;
  }

  public function buildAdvancedFieldset(FormStateInterface $form_state, Config $config) {
    $elements = [];

    return $elements;
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
    parent::submitForm($form, $form_state);

    $this->config('google_tag_manager.configuration')
      ->set('container_id', $form_state->getValue('container_id'))
      ->set('dev_gtm_auth', $form_state->getValue('dev_gtm_auth'))
      ->set('dev_gtm_preview', $form_state->getValue('dev_gtm_preview'))
      ->set('stage_gtm_auth', $form_state->getValue('stage_gtm_auth'))
      ->set('stage_gtm_preview', $form_state->getValue('stage_gtm_preview'))
      ->set('live_gtm_auth', $form_state->getValue('live_gtm_auth'))
      ->set('live_gtm_preview', $form_state->getValue('live_gtm_preview'))
      ->save();

    $this->config('google_tag_manager.configuration')
      ->set('environment', $form_state->getValue('environment'))
      ->save();
  }

}
