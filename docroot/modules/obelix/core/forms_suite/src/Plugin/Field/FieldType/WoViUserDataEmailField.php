<?php

namespace Drupal\forms_suite\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Render\Element\Email;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Plugin implementation of the 'wovi_user_data_email_field' field type.
 *
 * @FieldType(
 *   id = "wovi_user_data_email_field",
 *   label = @Translation("User data email field"),
 *   description = @Translation("Forms suite user data email field"),
 *   default_widget = "wovi_user_data_email_widget",
 *   default_formatter = "wovi_formatter",
 *   category = @Translation("Forms suite")
 * )
 */
class WoViUserDataEmailField extends FormsSuiteField
{

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition)
  {
    // Prevent early t() calls by using the TranslatableMarkup.
    $properties['salutation'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('salutation'))
      ->setSetting('case_sensitive', $field_definition->getSetting('case_sensitive'));
    $properties['firstName'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('firstName'))
      ->setSetting('case_sensitive', $field_definition->getSetting('case_sensitive'));
    $properties['surname'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('surname'))
      ->setSetting('case_sensitive', $field_definition->getSetting('case_sensitive'));
    $properties['phonePrivate'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('phonePrivate'))
      ->setSetting('case_sensitive', $field_definition->getSetting('case_sensitive'));
    $properties['email'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('email'))
      ->setSetting('case_sensitive', $field_definition->getSetting('case_sensitive'));

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition)
  {
    $schema = [
      'columns' => [
        'salutation' => [
          'type' => 'varchar',
          'length' => 10,
          'not null' => FALSE,
        ],
        'firstName' => [
          'type' => 'varchar',
          'length' => 100,
          'not null' => FALSE,
        ],
        'surname' => [
          'type' => 'varchar',
          'length' => 100,
          'not null' => FALSE,
        ],
        'phonePrivate' => [
          'type' => 'varchar',
          'length' => 100,
          'not null' => FALSE,
        ],
        'email' => [
          'type' => 'varchar',
          'length' => Email::EMAIL_MAX_LENGTH,
          'not null' => FALSE,
        ],
      ],
    ];

    return $schema;
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty()
  {
    $value = [
      $this->get('salutation')->getValue(),
      $this->get('firstName')->getValue(),
      $this->get('surname')->getValue(),
      $this->get('phonePrivate')->getValue(),
      $this->get('email')->getValue(),
    ];
    $empty = FALSE;
    foreach ($value as $item) {
      $empty &= empty($item);
    }
    return $empty;
  }

}
