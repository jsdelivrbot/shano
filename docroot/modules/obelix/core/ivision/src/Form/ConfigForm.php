<?php

namespace Drupal\ivision\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class ConfigForm.
 *
 * @package Drupal\ivision\Form
 */
class ConfigForm extends ConfigFormBase
{

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames()
  {
    return [
      'ivision.config',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId()
  {
    return 'ivision_config_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state)
  {
    $config = $this->config('ivision.config');
    $values = $config->get('ivision');

    $form['web_reference_id'] = [
      '#type' => 'number',
      '#title' => $this->t('start value web reference ID '),
      '#default_value' => (isset($values['web_reference_id'])) ? $values['web_reference_id'] : 0,
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

    $this->config('ivision.config')
      ->set('ivision', $datasources)
      ->save();
  }

}
