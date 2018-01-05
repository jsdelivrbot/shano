<?php

/**
 * @file
 * Contains \Drupal\forms_suite\DataHandler.
 */

namespace Drupal\forms_suite;

use Drupal\Core\Field\FieldItemList;
use Drupal\Core\Form\FormState;
use Drupal\Core\Form\FormStateInterface;
use Drupal\field\Entity\FieldConfig;


/**
 * Provides a class for handling form data.
 */
class DataHandler implements DataHandlerInterface
{


  /**
   * @var array
   */
  private $form;

  /**
   * @var array
   */
  private $fields_complete = [];

  /**
   * @var MessageInterface
   */
  private $message;

  /**
   * @var array
   */
  private $fields = [];

  private $placeholders = [];

  /**
   * DataHandler constructor.
   * @param array $fields
   * @param MessageInterface $message
   * @param array $form
   */
  public function __construct(array $fields, MessageInterface $message, array $form = NULL)
  {
    // build default form. Needed for token select key value mapping.
    if (!$form) {
      $form = \Drupal::entityTypeManager()
        ->getFormObject($message->getEntityTypeId(), 'default');
      $form->setEntity($message);
      $form_state = new FormState();
      $this->form = \Drupal::formBuilder()->buildForm($form, $form_state);
    } else {
      $this->form = $form;
    }

    $this->fields_complete = $fields;
    $this->message = $message;
  }

  /**
   * @param null $reply
   * @return string
   */
  public function getReply($reply = NULL)
  {
    $token_service = \Drupal::token();
    if ($reply === NULL) {
      $reply = $this->message->getForm()->getReply();
    }
    $fields = $this->keyValueDataMapping($this->getFields());
    $placeholder_values = $this->getPlaceholderValues($this->convertFieldKeys($fields));
    return $token_service->replace($reply, $placeholder_values);
  }

  /**
   * @return array
   */
  static public function getTokenList()
  {
    $token_service = \Drupal::token();
    $tokens = [];
    $info = $token_service->getInfo();
    foreach ($info as $key => $value) {
      foreach ($value as $type => $token) {
        if (strpos($type, 'wovi_') !== FALSE) {
          $tokens[$key][$type] = $token;
        }
      }
    }

    return $tokens;
  }


  /**
   * @param array $values
   * @return array
   */
  public function getPlaceholderValues(array $values)
  {
    return $this->convertPlaceholderValues(array_merge_recursive($values, $this->placeholders));
  }

  /**
   * Convert placeholder values with custom conditions.
   *
   * @param $values
   */
  public function convertPlaceholderValues($values)
  {
    foreach ($values as &$field_values) {
      foreach ($field_values as $element_name => &$element_value) {
        switch ($element_name) {
          case 'IBAN' :
          case 'swiftCode' :
          case 'bankBranchNo' :
          case 'bankAccountNo' :
            // convert the string in X except the first 4 chars.
            if(!empty($element_value)){
              $visible_chars = 4;
              $element_value = substr($element_value, 0, $visible_chars) . str_repeat('X', strlen($element_value) - $visible_chars);
            }
            break;
        }
      }
    }
    return $values;
  }

  /**
   * @param array $values
   * @return array
   */
  public function setPlaceholderValues(array $values)
  {
    $this->placeholders = $values;
  }

  /**
   * @return array
   */
  public function getFields()
  {
    if (empty($this->fields)) {
      $values = $this->fields_complete;
      $this->fieldsToArray($values);
      $this->fields = self::searchFields($values);
    }
    return $this->fields;
  }

  /**
   * Convert FieldItemList objects to a value array.
   *
   * @param &$values
   */
  private function fieldsToArray(&$values)
  {
    foreach ($values as &$value) {
      if ($value instanceof FieldItemList) {
        /** @var FieldItemList $value */
        $value = $value->getValue();
        if (isset($value[0])) {
          $value = $value[0];
        }
      }
    }
  }

  /**
   * @param array $fields
   * @return array
   */
  private function keyValueDataMapping(array $fields)
  {
    foreach ($fields as $field_name => &$field_value) {
      foreach ($field_value as $item_name => &$item_value) {
        if (isset($this->form[$field_name]['widget'][$item_name])) {
          $item_definition = $this->form[$field_name]['widget'][$item_name];
          if (isset($item_definition['#options'][$item_value])) {
            $item_value = $item_definition['#options'][$item_value];
          }
        }
      }
    }
    return $fields;
  }

  static public function searchFields($values)
  {
    $found = [];
    foreach ($values as $key => $value) {
      if (strpos($key, 'field_') !== FALSE) {
        $found[$key] = $value;
      }
    }
    return $found;
  }

  /**
   * @param array $fields
   * @return array
   */
  public function convertFieldKeys(array $fields)
  {
    $fields_converted = [];
    foreach ($fields as $field_key => $field_value) {
      $field_definitions = $this->message->getFieldDefinitions();
      /** @var FieldConfig $field_definition */
      $field_definition = $field_definitions[$field_key];
      $new_key = $field_definition->get('field_type');
      $fields_converted[$new_key] = $field_value;
    }
    return $fields_converted;
  }

  /**
   * Checks the incident type against special conditions and
   * returns the correct incident type.
   *
   * @param $incident_type
   * @return string Returns the incident type
   * Returns the incident type
   */
  public function incidentConditions($incident_type)
  {

    // @todo also have a handler for incident types in the ivision send class. fix to one solution.
    if (isset($this->fields['field_suggestion']['suggestion'])) {
      switch ($this->fields['field_suggestion']['suggestion']) {
        case 1:
          $incident_type = 'AUTOMATISCHER MAIL-VORSCHLAG';
          break;
        case 2:
          $incident_type = 'PATENSCHAFT - ANFRAGEN';
          break;
      }
    }

    return $incident_type;
  }


}
