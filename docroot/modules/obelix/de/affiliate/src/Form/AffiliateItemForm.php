<?php

namespace Drupal\affiliate\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for Affiliate edit forms.
 *
 * @ingroup affiliate
 */
class AffiliateItemForm extends ContentEntityForm {
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var $entity \Drupal\affiliate\Entity\AffiliateItem */
    $form = parent::buildForm($form, $form_state);
    $entity = $this->entity;

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;
    $status = parent::save($form, $form_state);

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Affiliate.', [
          '%label' => $entity->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Affiliate.', [
          '%label' => $entity->label(),
        ]));
    }
    $form_state->setRedirect('entity.affiliate.canonical', ['affiliate' => $entity->id()]);
  }

}
