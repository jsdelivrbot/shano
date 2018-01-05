<?php

namespace Drupal\wv_site\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * General site blocks config form.
 */
class BlocksConfigForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'wv_site_config_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'wv_site.settings.blocks',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('wv_site.settings.blocks');

    $form['wv_common'] = array(
      '#type' => 'vertical_tabs',
      '#title' => $this->t('Blocks Settings'),
    );

    $form['footer_contacts'] = array(
      '#type' => 'details',
      '#title' => $this->t('Footer: contacts'),
      '#group' => 'wv_common',
    );

    $form['footer_contacts']['footer_contacts_left'] = array(
      '#type' => 'textarea',
      '#format' => 'html',
      '#title' => $this->t('Left'),
      '#description' => $this->t('You can set BCC to see all mails that sponsors receive upon donation forms submits'),
      '#default_value' => $config->get('footer_contacts_left')
    );

    $form['forms'] = array(
      '#type' => 'details',
      '#title' => $this->t('Forms'),
      '#group' => 'wv_common',
    );

    $form['forms']['donation_mail_bcc'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('All donation autoreply mails BCC'),
      '#description' => $this->t('You can set BCC to see all mails that sponsors receive upon donation forms submits'),
      '#default_value' => $config->get('donation_mail_bcc')
    );

    $form['forms']['disable_children_form_block'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Disable children form blocking'),
      '#description' => $this->t('If this is disabled children will not be blocked if user access the forms.'),
      '#default_value' => $config->get('disable_children_form_block')
    );

    $form['forms']['children_form_block_timeout'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Children form block timeout'),
      '#description' => $this->t('Please set it in seconds by default 1800 seconds (30 mins) is used if this field not set or has 0 value.'),
      '#default_value' => $config->get('children_form_block_timeout'),
      '#states' => array(
        // Only show this field when the 'disable_children_form_block' checkbox is disabled.
        'visible' => array(
          ':input[name="disable_children_form_block"]' => array('checked' => FALSE),
        ),
      ),
    );

    $form['countries'] = array(
      '#type' => 'details',
      '#title' => $this->t('Countries'),
      '#group' => 'wv_common',
    );

    $form['countries']['restrict_countries_by_active_projects'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Hide countries without active projects'),
      '#default_value' => $config->get('restrict_countries_by_active_projects')
    );

    $form['editorial_child_widget'] = array(
      '#type' => 'details',
      '#title' => $this->t('Editorial child widget'),
      '#group' => 'wv_common',
    );

    $form['editorial_child_widget']['skip_default_countries'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Skip default countries random selector.'),
      '#default_value' => $config->get('skip_default_countries')
    );

    $form['child_finder'] = array(
      '#type' => 'details',
      '#title' => $this->t('Child finder'),
      '#group' => 'wv_common',
    );

    $form['child_finder']['is_simple_find_process'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Simple find process.'),
      '#description' => $this->t('Use only one page for finder.'),
      '#default_value' => $cf_config->get('is_simple_find_process')
    );

    $form['child_finder']['child_page_route'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Child finder page route.'),
      '#description' => $this->t(
        'This is not url but route, please contact support if you are not sure which route you need to set.'
      ),
      '#default_value' => $cf_config->get('child_page_route')
        ?: 'child_sponsorship.child_sponsorship_controller_ChildSponsorshipAction',
    );

    $form['child_finder']['sponsorship_suggestion_uri'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Suggestion form uri.'),
      '#description' => $this->t('This is relative uri.'),
      '#states' => array(
        'visible' => array(
          ':input[name="is_simple_find_process"]' => array('checked' => FALSE),
        ),
      ),
      '#default_value' => $cf_config->get('sponsorship_suggestion_uri')
        ?: '/forms/child_sponsorship_suggestion',
    );

    $form['child_finder']['sponsorship_direct_uri'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Direct form uri.'),
      '#description' => $this->t('This is relative uri.'),
      '#states' => array(
        'visible' => array(
          ':input[name="is_simple_find_process"]' => array('checked' => FALSE),
        ),
      ),
      '#default_value' => $cf_config->get('sponsorship_direct_uri')
        ?: '/forms/child_sponsorship_direct',
    );

    $form['children'] = array(
      '#type' => 'details',
      '#title' => $this->t('Children'),
      '#group' => 'wv_common',
    );

    $form['children']['hidden_child_endpoint'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Hidden child endpoint.'),
      '#description' => $this->t(
        'Anybody with this link can access even not published child page.'
      ),
      '#default_value' => $c_config->get('hidden_child_endpoint')
        ?: '/even-voorstellen',
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
    $gv = function ($key) use ($form_state) {
      return $form_state->getValue($key);
    };
    
    // Retrieve the configuration.
    $this->config('wv_site.settings')
      // Set the submitted configuration setting.
      ->set('restrict_countries_by_active_projects', $gv('restrict_countries_by_active_projects'))
      ->set('donation_mail_bcc', $gv('donation_mail_bcc'))
      ->set('skip_default_countries', $gv('skip_default_countries'))
      ->set('disable_children_form_block', $gv('disable_children_form_block'))
      ->set('children_form_block_timeout', $gv('children_form_block_timeout'))
      ->save();

    $this->config('wv_site.settings.child_finder')
      ->set('is_simple_find_process', $gv('is_simple_find_process'))
      ->set('child_page_route', $gv('child_page_route'))
      ->set('sponsorship_suggestion_uri', $gv('sponsorship_suggestion_uri'))
      ->set('sponsorship_direct_uri', $gv('sponsorship_direct_uri'))
      ->save();

    $this->config('wv_site.settings.children')
      ->set('hidden_child_endpoint', $gv('hidden_child_endpoint'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
