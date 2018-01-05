<?php

namespace Drupal\forms_suite\Event;

use Drupal\Core\Form\FormStateInterface;
use Drupal\forms_suite\Entity\Form;
use Drupal\forms_suite\FormInterface;
use Symfony\Component\EventDispatcher\Event;

class FormEditEvent extends Event
{
  /**
   * @var array
   */
  private $form;
  /**
   * @var FormStateInterface
   */
  private $form_state;
  /**
   * @var FormInterface
   */
  private $form_entity;

  /**
   * FormEditEvent constructor.
   * @param array $form
   * @param FormStateInterface $form_state
   * @param FormInterface $form_entity
   */
  public function __construct(array &$form, FormStateInterface &$form_state, FormInterface &$form_entity)
  {
    $this->form = &$form;
    $this->form_state = &$form_state;
    $this->form_entity = &$form_entity;
  }

  /**
   * @return array
   */
  public function getForm()
  {
    return $this->form;
  }

  /**
   * @param array $form
   */
  public function setForm($form)
  {
    $this->form = $form;
  }

  /**
   * @return FormStateInterface
   */
  public function getFormState()
  {
    return $this->form_state;
  }

  /**
   * @param FormStateInterface $form_state
   */
  public function setFormState($form_state)
  {
    $this->form_state = $form_state;
  }

  /**
   * @return FormInterface
   */
  public function getFormEntity()
  {
    return $this->form_entity;
  }

  /**
   * @param FormInterface $form_entity
   */
  public function setFormEntity($form_entity)
  {
    $this->form_entity = $form_entity;
  }

}
