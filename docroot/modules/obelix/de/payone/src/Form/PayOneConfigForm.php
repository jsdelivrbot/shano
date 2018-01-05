<?php

namespace Drupal\payone\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class PayOneConfigForm.
 *
 * @package Drupal\payone\Form
 */
class PayOneConfigForm extends ConfigFormBase
{

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames()
  {
    return [
      'payone.config',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId()
  {
    return 'pay_one_config_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state)
  {
    $config = $this->config('payone.config');
    $values = $config->get('payone');


    $form['portal_id'] = [
      '#type' => 'number',
      '#title' => $this->t('portal id'),
      '#description' => $this->t('Portal ID number'),
      '#default_value' => $values['portal_id'],
    ];

    $form['portal_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('portal_key'),
      '#description' => $this->t('Name of the portal key'),
      '#default_value' => $values['portal_key'],
    ];

    $form['aid'] = [
      '#type' => 'number',
      '#title' => $this->t('aid'),
      '#description' => $this->t('aid number'),
      '#default_value' => $values['aid'],
    ];

    $form['mode'] = [
      '#type' => 'select',
      '#title' => $this->t('mode'),
      '#description' => $this->t('Select the mode'),
      '#options' => [
        'live' => 'live',
        'test' => 'test',
      ],
      '#default_value' => $values['mode'],
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

    $payone = $form_state->cleanValues()->getValues();

    $this->config('payone.config')
      ->set('payone', $payone)
      ->save();
  }

}
