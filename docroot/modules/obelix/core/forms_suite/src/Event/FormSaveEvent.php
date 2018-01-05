<?php

namespace Drupal\forms_suite\Event;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Form\FormStateInterface;
use Drupal\forms_suite\Entity\Message;

class FormSaveEvent extends FormBaseEvent
{
  /**
   * @var Message
   */
  private $message;

  /**
   * FormEvent constructor.
   * @param array $form
   * @param FormStateInterface $formState
   * @param Message $message
   */
  public function __construct(array &$form, FormStateInterface &$formState, Message &$message)
  {
    parent::__construct($form, $formState);
    $this->message = &$message;
  }

  /**
   * @return Message
   */
  public function getMessage()
  {
    return $this->message;
  }

  /**
   * @param Message $message
   */
  public function setMessage($message)
  {
    $this->message = $message;
  }

}
