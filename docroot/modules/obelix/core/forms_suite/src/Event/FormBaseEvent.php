<?php

namespace Drupal\forms_suite\Event;

use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\EventDispatcher\Event;

class FormBaseEvent extends Event {
  /**
   * @var array
   */
  private $form;
  /**
   * @var FormStateInterface
   */
  private $form_state;

  /**
   * FormEvent constructor.
   * @param array $form
   * @param FormStateInterface $form_state
   */
  public function __construct(array &$form, FormStateInterface &$form_state) {
    $this->form = &$form;
    $this->form_state = &$form_state;
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

}
