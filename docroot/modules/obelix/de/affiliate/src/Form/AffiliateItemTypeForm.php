<?php

namespace Drupal\affiliate\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class AffiliateItemTypeForm.
 *
 * @package Drupal\affiliate\Form
 */
class AffiliateItemTypeForm extends EntityForm {
  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $affiliate_type = $this->entity;
    $form['label'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $affiliate_type->label(),
      '#description' => $this->t("Label for the Affiliate type."),
      '#required' => TRUE,
    );

    $form['id'] = array(
      '#type' => 'machine_name',
      '#default_value' => $affiliate_type->id(),
      '#machine_name' => array(
        'exists' => '\Drupal\affiliate\Entity\AffiliateItemType::load',
      ),
      '#disabled' => !$affiliate_type->isNew(),
    );

    /* You will need additional form elements for your custom properties. */

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $affiliate_type = $this->entity;
    $status = $affiliate_type->save();

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Affiliate type.', [
          '%label' => $affiliate_type->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Affiliate type.', [
          '%label' => $affiliate_type->label(),
        ]));
    }
    $form_state->setRedirectUrl($affiliate_type->urlInfo('collection'));
  }

}
