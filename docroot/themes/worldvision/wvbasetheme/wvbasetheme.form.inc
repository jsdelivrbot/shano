<?php

/**
 * @file
 * Form related logic.
 */

use \Drupal\Core\Form\FormStateInterface;

function wvbasetheme_form_system_theme_settings_alter(&$form, FormStateInterface &$form_state, $form_id = NULL) {
  // Work-around for a core bug affecting admin themes. See issue #943212.
  if (isset($form_id)) {
    return;
  }

  $form['html_head_custom_code'] = array(
    '#type'          => 'textarea',
    '#title'         => t('Custom HTML head code'),
    '#default_value' => theme_get_setting('html_head_custom_code'),
    '#description'   => t('Place any code that will be put just above closing head tag.'),
  );
}
