<?php

namespace Drupal\forms_suite\Event;

use Drupal\forms_suite\DataHandlerInterface;
use Drupal\forms_suite\MessageInterface;
use Symfony\Component\EventDispatcher\Event;

class MailHandlerEvent extends Event {
  /**
   * @var MessageInterface
   */
  private $message;
  /**
   * @var DataHandlerInterface
   */
  private $data_handler;
  /**
   * @var array
   */
  private $values;
  /**
   * @var array
   */
  private $params;

  /**
   * MailHandlerEvent constructor.
   * @param MessageInterface $message
   * @param DataHandlerInterface $data_handler
   * @param array $values
   * @param array $params
   */
  public function __construct(MessageInterface &$message, DataHandlerInterface &$data_handler, array &$values, array &$params) {

    $this->message = &$message;
    $this->data_handler = &$data_handler;
    $this->values = &$values;
    $this->params = &$params;
  }

  /**
   * @return array
   */
  public function getValues()
  {
    return $this->values;
  }

  /**
   * @param array $values
   */
  public function setValues($values)
  {
    $this->values = $values;
  }

  /**
   * @return DataHandlerInterface
   */
  public function getDataHandler()
  {
    return $this->data_handler;
  }

  /**
   * @param DataHandlerInterface $data_handler
   */
  public function setDataHandler($data_handler)
  {
    $this->data_handler = $data_handler;
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

  /**
   * @return array
   */
  public function getParams()
  {
    return $this->params;
  }

  /**
   * @param array $params
   */
  public function setParams($params)
  {
    $this->params = $params;
  }

}
