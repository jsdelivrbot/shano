<?php

namespace Drupal\obelix_main\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure settings for contactf of our company.
 */
class obelixMainSettingsForm extends ConfigFormBase {
  /** 
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'obelix_main_admin_settings';
  }

  /** 
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'obelix_main.settings',
    ];
  }

  /** 
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('obelix_main.settings');

    $form['buttons'] = array(
      '#type' => 'details',
      '#title' => $this->t('Buttons settings'),
      '#open' => TRUE,
    );

    $form['buttons']['becomeGodfatherUrl'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Become a godfather URL'),
      '#description' => $this->t('Url for button "Become a godfather"'),
      '#default_value' => $config->get('becomeGodfatherUrl'),
    );

    return parent::buildForm($form, $form_state);
  }

  /** 
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
    $values = $form_state->getValues();
    // Retrieve the configuration
    $this->config('obelix_main.settings')
    ->set('becomeGodfatherUrl', $values['becomeGodfatherUrl'])
    ->save();
  }
}