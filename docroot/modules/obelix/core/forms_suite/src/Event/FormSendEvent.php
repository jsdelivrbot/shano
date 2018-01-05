<?php

namespace Drupal\forms_suite\Event;

use Drupal\forms_suite\FormInterface;
use Symfony\Component\EventDispatcher\Event;

class FormSendEvent extends Event {
  /**
   * @var FormInterface
   */
  private $form;

  /**
   * FormSendEvent constructor.
   * @param FormInterface $form
   */
  public function __construct(FormInterface &$form) {
    $this->form = &$form;
  }

  /**
   * @return FormInterface
   */
  public function getForm()
  {
    return $this->form;
  }

  /**
   * @param FormInterface $form
   */
  public function setForm($form)
  {
    $this->form = $form;
  }

}
