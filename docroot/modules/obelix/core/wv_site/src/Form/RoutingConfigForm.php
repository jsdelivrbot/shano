<?php

namespace Drupal\wv_site\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * General site routes administration form.
 */
class RoutingConfigForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'wv_site_routing_config_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'wv_site.settings.routing',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('wv_site.settings.routing');

    $form['wv_routing'] = array(
      '#type' => 'vertical_tabs',
      '#title' => $this->t('Routing Settings'),
    );

    $form['pages_links'] = array(
      '#type' => 'details',
      '#title' => $this->t('Pages Links'),
      '#group' => 'wv_routing',
    );

    $form['pages_links']['gift_type_all_gifts_url'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Gift type: all gifts'),
      '#description' => $this->t('Please choose url this link will point to.'),
      '#default_value' => $config->get('gift_type_all_gifts_url')
    );

    $form['forms_urls'] = array(
      '#type' => 'details',
      '#title' => $this->t('Forms urls'),
      '#group' => 'wv_routing',
    );

    $form['forms_urls']['newsletter_form_url'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Newsletter'),
      '#description' => $this->t('Please choose url for this url, it is used by links pointing to it.'),
      '#default_value' => $config->get('newsletter_form_url')
    );

    $form['forms_urls']['sponsor_form_url'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Sponsorship'),
      '#description' => $this->t('Please choose url for this url, it is used by links pointing to it.'),
      '#default_value' => $config->get('sponsor_form_url')
    );

    $form['forms_urls']['sponsor_child_form_url'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Sponsor Child'),
      '#description' => $this->t('Please choose url for this url, it is used by links pointing to it.'),
      '#default_value' => $config->get('sponsor_child_form_url')
    );

    $form['forms_urls']['suggestion_email_form_url'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Suggestion email'),
      '#description' => $this->t('Please choose url for this url, it is used by links pointing to it.'),
      '#default_value' => $config->get('suggestion_email_form_url')
    );

    $form['forms_urls']['suggestion_post_form_url'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Suggestion post'),
      '#description' => $this->t('Please choose url for this url, it is used by links pointing to it.'),
      '#default_value' => $config->get('suggestion_post_form_url')
    );

    $form['forms_urls']['child_sponsorship_form_in_english'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Child sponsorship form in English'),
      '#description' => $this->t('Please choose url for this url, it is used by the child direct sponsorship form in English.'),
      '#default_value' => $config->get('child_sponsorship_form_in_english')
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

    // Retrieve the configuration.
    $config = $this->config('wv_site.settings.routing');

    $fields = [
      'gift_type_all_gifts_url',
      'newsletter_form_url',
      'sponsor_form_url',
      'suggestion_email_form_url',
      'suggestion_post_form_url',
      'sponsor_child_form_url',
      'child_sponsorship_form_in_english',
    ];

    foreach ($fields as $field_name) {
      // Set the submitted configuration settings.
      $config->set($field_name,  $form_state->getValue($field_name));
    }

    $config->save();

    parent::submitForm($form, $form_state);
  }

}
