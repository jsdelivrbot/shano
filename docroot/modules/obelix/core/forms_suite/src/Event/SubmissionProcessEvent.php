<?php

namespace Drupal\forms_suite\Event;

use Drupal\forms_suite\FormInterface;
use Drupal\forms_suite\MessageInterface;
use Symfony\Component\EventDispatcher\Event;

class SubmissionProcessEvent extends Event {
  /**
   * @var MessageInterface
   */
  private $message;
  /**
   * @var FormInterface
   */
  private $form;


  /**
   * FormEvent constructor.
   * @param MessageInterface $message
   * @param FormInterface $form
   */
  public function __construct(MessageInterface &$message, FormInterface &$form) {

    $this->message = &$message;
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

  /**
   * @return MessageInterface
   */
  public function getMessage()
  {
    return $this->message;
  }

  /**
   * @param MessageInterface $message
   */
  public function setMessage($message)
  {
    $this->message = $message;
  }


}
