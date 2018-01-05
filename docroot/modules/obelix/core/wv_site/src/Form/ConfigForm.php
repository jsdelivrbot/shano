<?php

namespace Drupal\wv_site\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * General site administration form.
 */
class ConfigForm extends ConfigFormBase {

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
      'wv_site.settings',
      'wv_site.settings.child_finder',
      'wv_site.settings.children',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('wv_site.settings');
    $cf_config = $this->config('wv_site.settings.child_finder');
    $c_config = $this->config('wv_site.settings.children');

    $form['wv_common'] = array(
      '#type' => 'vertical_tabs',
      '#title' => $this->t('Common Settings'),
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

    $form['forms']['children_user_limit'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Children user limit'),
      '#description' => $this->t(
        'How many children can be seen by user during his session. Value is int 0 stands for disabled.'
      ),
      '#default_value' => $config->get('children_user_limit'),
    );

    $form['forms']['allow_different_children_from_the_same_country'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Allow different children from the same country'),
      '#description' => $this->t(
        'Lets user see different children from the same country on editorial pages & in other places (child finder).'
      ),
      '#default_value' => $config->get('allow_different_children_from_the_same_country'),
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

    $form['child_finder']['hide_childfinder_globally'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Hide childfinder widget globally.'),
      '#description' => $this->t('Hides the childfinder widget at the bottom of editorial pages globally.'),
      '#default_value' => $cf_config->get('hide_childfinder_globally')
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

    $form['children']['hidden_children'] = array(
      '#type' => 'details',
      '#open' => TRUE,
      '#title' => $this->t('Hidden Children'),
      '#group' => 'children',
    );

    $form['children']['hidden_children']['hidden_child_endpoint'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Hidden child endpoint.'),
      '#description' => $this->t(
        'Anybody with this link can access even not published child page.'
      ),
      '#default_value' => $c_config->get('hidden_child_endpoint')
        ?: '/even-voorstellen',
    );

    $form['children']['hidden_children']['hidden_child_layout'] = array(
      '#type' => 'text_format',
      '#format' => 'html',
      '#title' => $this->t('Hidden child layout.'),
      '#description' => $this->t('Variables: {{ left_region }}, {{ right_region }} {{ sponsorship_child_section }}.'),
      '#default_value' => $c_config->get('hidden_child_layout'),
    );

    $form['children']['hidden_children']['hidden_children_left_region'] = array(
      '#type' => 'text_format',
      '#format' => 'html',
      '#title' => $this->t('Hidden child left region.'),
      '#description' => $this->t('Variables: project_name, childtext1, childtext2, childtext3, projecttext1, child_firstname'),
      '#default_value' => $c_config->get('hidden_children_left_region'),
    );

    $form['children']['hidden_children']['hidden_children_right_region'] = array(
      '#type' => 'textarea',
      '#title' => $this->t('Hidden child right region.'),
      '#description' => $this->t('Variables: -'),
      '#default_value' => $c_config->get('hidden_children_right_region'),
    );

    $form['certificates'] = array(
      '#type' => 'details',
      '#title' => $this->t('Certificates'),
      '#group' => 'wv_common',
    );

    $allow_post_option = $config->get('allow_post_option');

    $form['certificates']['allow_post_option'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Allow post option.'),
      '#description' => $this->t('Allow user request certificates by post.'),
      '#default_value' => isset($allow_post_option) ? $allow_post_option : TRUE,
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
      ->set('children_user_limit', $gv('children_user_limit'))
      ->set('allow_different_children_from_the_same_country', $gv('allow_different_children_from_the_same_country'))
      ->set('allow_post_option', $gv('allow_post_option'))
      ->save();

    $this->config('wv_site.settings.child_finder')
      ->set('hide_childfinder_globally', $gv('hide_childfinder_globally'))
      ->set('is_simple_find_process', $gv('is_simple_find_process'))
      ->set('child_page_route', $gv('child_page_route'))
      ->set('sponsorship_suggestion_uri', $gv('sponsorship_suggestion_uri'))
      ->set('sponsorship_direct_uri', $gv('sponsorship_direct_uri'))
      ->save();

    $this->config('wv_site.settings.children')
      ->set('hidden_child_endpoint', $gv('hidden_child_endpoint'))
      ->set('hidden_child_layout', $gv('hidden_child_layout')['value'])
      ->set('hidden_children_left_region', $gv('hidden_children_left_region')['value'])
      ->set('hidden_children_right_region', $gv('hidden_children_right_region'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
