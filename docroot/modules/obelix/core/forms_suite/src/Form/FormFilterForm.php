<?php
/**
 * @file
 * Contains \Drupal\forms_suite\Form\FormFilterForm.
 */
namespace Drupal\forms_suite\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

class FormFilterForm extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'forms_suite_filter_form';
  }
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $label_value = \Drupal::request()->get('label');

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => t('Form'),
      '#default_value' => $label_value,
    ];

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Apply'),
    ];
    if ($label_value) {
      $form['actions']['reset'] = [
        '#type' => 'submit',
        '#value' => $this->t('Reset'),
      ];
    }
    return $form;
  }
  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $data = $form_state->getValue('label');
    // Reset the query parameters if the reset button is clicked.
    if ($form_state->getTriggeringElement()['#value']->getUntranslatedString() == 'Reset') {
      $data = NULL;
    }

    $query = ($data) ? ['label' => $data] : [];

    $url = Url::fromRoute('entity.form.collection', [], ['query' => $query]);
    $form_state->setRedirectUrl($url);
  }
}
