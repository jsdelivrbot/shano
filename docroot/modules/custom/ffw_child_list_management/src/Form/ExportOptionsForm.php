<?php

namespace Drupal\ffw_child_list_management\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\ffw_child_list_management\Controller\ChildDataController;

/**
 * Class ExportOptionsForm.
 */
class ExportOptionsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames()
  {
    return [
      'export_options_form.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'export_options_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $config = $this->config('export_options_form.settings');
    $values = $config->getRawData();

    $form['export_settings'] = array(
      '#type' => 'fieldset',
      '#title' => $this->t('Export settings'),
    );

    $form['export_settings']['select_what_to_export'] = [
      '#type' => 'radios',
      '#title' => $this->t('Select what to export:'),
      '#options' => ['field_child_givenname' => $this->t('Firstname'), 'field_child_familyname' => $this->t('Family name'), 'field_child_birthdate' => $this->t('Birthdate')],
      '#default_value' => 'field_child_givenname',

    ];

    $form['export_settings']['delimiter_symbol'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Delimiter symbol'),
      '#size' => 10,
      '#maxlength' => 1,
      '#required' => TRUE,
      '#default_value' => $values['delimiter_symbol'],
    );

    $form['export_settings']['enclosure_symbol'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Enclosure symbol'),
      '#size' => 10,
      '#maxlength' => 1,
      '#required' => TRUE,
      '#default_value' => $values['enclosure_symbol'],
    );

    $form['export_settings']['actions'] = [
      '#type' => 'actions',
    ];
    $form['export_settings']['actions']['export_to_csv_button'] = [
      '#type' => 'submit',
      '#description' => $this->t('123'),
      '#value' => $this->t('Export to CSV'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
    $values = $form_state->getValues();
    // Retrieve the configuration
    $this->config('export_options_form.settings')
      ->set('delimiter_symbol', $values['delimiter_symbol'])
      ->set('enclosure_symbol', $values['enclosure_symbol'])
      ->save();

    $result = ChildDataController::getOptions($form_state->getValue('select_what_to_export'));
    ChildDataController::generateCsv($result, ',', '"');
  }
}
