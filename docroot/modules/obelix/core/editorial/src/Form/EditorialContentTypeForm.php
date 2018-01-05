<?php

/**
 * @file
 * Contains \Drupal\editorial\Form\EditorialContentTypeForm.
 */

namespace Drupal\editorial\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class EditorialContentTypeForm.
 *
 * @package Drupal\editorial\Form
 */
class EditorialContentTypeForm extends EntityForm {
  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $editorial_content_type = $this->entity;
    $form['label'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $editorial_content_type->label(),
      '#description' => $this->t("The label of this editorial content type."),
      '#required' => TRUE,
    );

    $form['id'] = array(
      '#type' => 'machine_name',
      '#default_value' => $editorial_content_type->id(),
      '#machine_name' => array(
        'exists' => '\Drupal\editorial\Entity\EditorialContentType::load',
      ),
      '#disabled' => !$editorial_content_type->isNew(),
    );

    $form['category'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Category'),
      '#maxlength' => 255,
      '#default_value' => $editorial_content_type->getCategory(),
      '#description' => $this->t("Category for the Editorial content type."),
      '#required' => TRUE,
    );

    $form['description'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Description'),
      '#maxlength' => 255,
      '#default_value' => $editorial_content_type->getDescription(),
      '#description' => $this->t("A short description about this block type."),
      '#required' => TRUE,
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $editorial_content_type = $this->entity;
    $status = $editorial_content_type->save();

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Editorial content type.', [
          '%label' => $editorial_content_type->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Editorial content type.', [
          '%label' => $editorial_content_type->label(),
        ]));
    }
    $form_state->setRedirectUrl($editorial_content_type->urlInfo('collection'));
  }

}
