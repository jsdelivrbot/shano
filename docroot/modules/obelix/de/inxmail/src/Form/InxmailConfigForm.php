<?php

namespace Drupal\inxmail\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class ConfigForm.
 *
 * @package Drupal\inxmail\Form
 */
class InxmailConfigForm extends ConfigFormBase
{

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames()
  {
    return [
      'inxmail.config',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId()
  {
    return 'config_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state)
  {
    $config = $this->config('inxmail.config');
    $values = $config->getRawData();

    $form['inxmail'] = [
      '#type' => 'details',
      '#title' => $this->t('Inxmail config'),
      '#open' => TRUE,
      '#tree' => TRUE,
    ];

    $form['inxmail']['domain'] = [
      '#title' => $this->t('Domain'),
      '#type' => 'textfield',
      '#default_value' => isset($values['inxmail']['domain']) ? $values['inxmail']['domain'] : '',
      '#description' => t('The domain of the inxmail service')
    ];

    $form['inxmail']['return_url'] = [
      '#title' => $this->t('Return URL'),
      '#type' => 'textfield',
      '#default_value' => isset($values['inxmail']['return_url']) ? $values['inxmail']['return_url'] : '',
      '#description' => t('The URL the user is redirected after inxmail process')
    ];

    $form['inxmail']['error_url'] = [
      '#title' => $this->t('Error URL'),
      '#type' => 'textfield',
      '#default_value' => isset($values['inxmail']['error_url']) ? $values['inxmail']['error_url'] : '',
      '#description' => t('The URL the user is redirected after inxmail process error')
    ];


    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state)
  {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    parent::submitForm($form, $form_state);

    $datasources = $form_state->cleanValues()->getValues();


    $this->config('inxmail.config')
      ->setData($datasources)
      ->save();
  }

}
