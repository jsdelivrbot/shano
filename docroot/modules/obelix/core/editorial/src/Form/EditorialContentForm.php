<?php

/**
 * @file
 * Contains \Drupal\editorial\Form\EditorialContentForm.
 */

namespace Drupal\editorial\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for Editorial content edit forms.
 *
 * @ingroup editorial_content
 */
class EditorialContentForm extends ContentEntityForm {
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var $entity \Drupal\editorial\Entity\EditorialContent */
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
        drupal_set_message($this->t('Created the %label Editorial content.', [
          '%label' => $entity->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Editorial content.', [
          '%label' => $entity->label(),
        ]));
    }
    $form_state->setRedirect('entity.editorial_content.canonical', ['editorial_content' => $entity->id()]);
  }

}
