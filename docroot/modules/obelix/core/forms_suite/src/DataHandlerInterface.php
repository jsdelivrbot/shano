<?php

/**
 * @file
 * Contains \Drupal\forms_suite\DataHandler.
 */

namespace Drupal\forms_suite;

use Drupal\Core\Form\FormStateInterface;


/**
 * Provides a class for handling form data.
 */
interface DataHandlerInterface
{

  /**
   * @param $reply
   * @return string
   */
  public function getReply($reply = NULL);

  static public function getTokenList();


  /**
   * @param array $values
   * @return array
   */
  public function getPlaceholderValues(array $values);

  /**
   * @param array $values
   * @return array
   */
  public function setPlaceholderValues(array $values);

  /**
   * @return array
   */
  public function getFields();

  /**
   * @param array $fields
   * @return array
   */
  public function convertFieldKeys(array $fields);

}
