<?php

/**
 * @file
 * Contains \Drupal\editorial\Form\EditorialContentSettingsForm.
 */

namespace Drupal\editorial\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class EditorialContentSettingsForm.
 *
 * @package Drupal\editorial\Form
 *
 * @ingroup editorial_content
 */
class EditorialContentSettingsForm extends FormBase {
  /**
   * Returns a unique string identifying the form.
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId() {
    return 'editorial_content_settings_form';
  }

  /**
   * Form submission handler.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Empty implementation of the abstract submit class.
  }


  /**
   * Defines the settings form for Editorial content entities.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   Form definition array.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['editorial_content_settings']['#markup'] = 'Settings form for Editorial content entities. Manage field settings here.';
    return $form;
  }

}
