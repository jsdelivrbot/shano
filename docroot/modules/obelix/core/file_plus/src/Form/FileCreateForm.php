<?php

namespace Drupal\file_plus\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\file\Entity\File;

/**
 * Class FileCreateForm.
 *
 * @package Drupal\file_plus\Form
 */
class FileCreateForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'file_create_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $allowed_extensions = 'jpg jpeg gif png gif pdf doc txt zip gzip gz tar bz';
    $mb_limit = 200;
    $form['file'] = [
      '#type' => 'managed_file',
      '#upload_location' => 'public://direct-upload',
      '#title' => $this->t('File'),
      '#required' => TRUE,
      '#upload_validators' => [
        'file_validate_extensions' => [$allowed_extensions],
        'file_validate_size' => [($mb_limit  * 1024 * 1024)],
      ],
      '#description' => t('Allowed file extensions: @extensions<br />File size limit: @mb_limit MB', [
        '@extensions' => $allowed_extensions,
        '@mb_limit' => $mb_limit,
      ]),
    ];
    $form['actions'] = ['#type' => 'actions'];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => t('Submit'),
      '#button_type' => 'primary',
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
    $account = \Drupal::currentUser();
    $fid = reset($form_state->getValue('file'));
    $file = File::load($fid);
    $file->setPermanent();
    $file->save();

    // Move the file to pdf folder if its pdf.
    if ($file->getMimeType() == 'application/pdf') {
      file_move($file, 'public://pdf');
    }

    \Drupal::service('file.usage')->add($file, 'file_plus', 'user', $account->id());
    $vars = [
      '@filename' => $file->getFilename(),
      '@filesize' => number_format($file->getSize() / 1024, 2) . ' kB',
    ];
    drupal_set_message($this->t('@filename (@filesize) uploaded to server successfully.', $vars));
    $redirect = Url::fromRoute('view.files.page_1');
    $form_state->setRedirectUrl($redirect);
  }

}
