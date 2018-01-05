<?php

namespace Drupal\obelix_contacts\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure settings for contactf of our company.
 */
class obelixContactsSettingsForm extends ConfigFormBase {
  /** 
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'obelix_contacts_admin_settings';
  }

  /** 
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'obelix_contacts.settings',
    ];
  }

  /** 
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('obelix_contacts.settings');

    $form['contactBlock'] = array(
      '#type' => 'details',
      '#title' => t('Contact Us block'),
      '#description' => t('Information which is dispayed in Contact Us block in footer of each page.'),
      '#open' => TRUE,
    );

    $form['contactBlock']['globalPhone'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Global phone'),
      '#default_value' => $config->get('globalPhone'),
    );

    $form['contactBlock']['openingHours'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Opening Hours'),
      '#default_value' => $config->get('openingHours'),
    );

    $form['contactBlock']['globalEmail'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Global email'),
      '#default_value' => $config->get('globalEmail'),
    );

    $form['donationBlock'] = array(
      '#type' => 'details',
      '#title' => t('Donation Info footer block'),
      '#description' => t('Accounting information in footer'),
      '#open' => TRUE,
    );

    $form['donationBlock']['generalDonationAccount'] = array(
      '#type' => 'text_format',
      '#title' => $this->t('General Donation Account'),
      '#format' => $config->get('generalDonationAccount.format'),
      '#default_value' => $config->get('generalDonationAccount.value'),
    );

    $form['donationBlock']['creditorIdNumber'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Creditor Identification Number'),
      '#default_value' => $config->get('creditorIdNumber'),
    );

    $form['socialAccountsBlock'] = array(
      '#type' => 'details',
      '#title' => t('Social networks links'),
      '#description' => t('Links to our company\'s pages in social services'),
      '#open' => TRUE,
    );

    $form['socialAccountsBlock']['ourFacebookPage'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Facebook'),
      '#default_value' => $config->get('ourFacebookPage'),
    );

    $form['socialAccountsBlock']['ourTwitterPage'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Twitter'),
      '#default_value' => $config->get('ourTwitterPage'),
    );

    $form['socialAccountsBlock']['ourGooglePlusPage'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Google Plus'),
      '#default_value' => $config->get('ourGooglePlusPage'),
    );

    $form['socialAccountsBlock']['ourXingPage'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Xing'),
      '#default_value' => $config->get('ourXingPage'),
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
    $this->config('obelix_contacts.settings')
    ->set('globalPhone', $values['globalPhone'])
    ->set('openingHours', $values['openingHours'])
    ->set('globalEmail', $values['globalEmail'])
    ->set('generalDonationAccount.value', $values['generalDonationAccount']['value'])
    ->set('generalDonationAccount.format', $values['generalDonationAccount']['format'])
    ->set('creditorIdNumber', $values['creditorIdNumber'])
    ->set('ourFacebookPage', $values['ourFacebookPage'])
    ->set('ourTwitterPage', $values['ourTwitterPage'])
    ->set('ourGooglePlusPage', $values['ourGooglePlusPage'])
    ->set('ourXingPage', $values['ourXingPage'])
    ->save();
  }
}