<?php

namespace Drupal\forms_suite\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Render\Element\Email;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Plugin implementation of the 'wovi_user_data_field' field type.
 *
 * @FieldType(
 *   id = "wovi_user_data_field",
 *   label = @Translation("User data field"),
 *   description = @Translation("Forms suite user data field"),
 *   default_widget = "wovi_user_data_widget",
 *   default_formatter = "wovi_formatter",
 *   category = @Translation("Forms suite")
 * )
 */
class WoViUserDataField extends FormsSuiteField {


  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    // Prevent early t() calls by using the TranslatableMarkup.
    $properties['salutation'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('salutation'))
      ->setSetting('case_sensitive', $field_definition->getSetting('case_sensitive'));

    $properties['jobTitleCode'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('jobTitleCode'))
      ->setSetting('case_sensitive', $field_definition->getSetting('case_sensitive'));
    $properties['initials'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('initials'))
      ->setSetting('case_sensitive', $field_definition->getSetting('case_sensitive'));
    $properties['firstName'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('firstName'))
      ->setSetting('case_sensitive', $field_definition->getSetting('case_sensitive'));
    $properties['middleName'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('middleName'))
      ->setSetting('case_sensitive', $field_definition->getSetting('case_sensitive'));
    $properties['surname'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('surname'))
      ->setSetting('case_sensitive', $field_definition->getSetting('case_sensitive'));
    $properties['companyName'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('companyName'))
      ->setSetting('case_sensitive', $field_definition->getSetting('case_sensitive'));
    $properties['street'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('street'))
      ->setSetting('case_sensitive', $field_definition->getSetting('case_sensitive'));
    $properties['houseNo'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('houseNo'))
      ->setSetting('case_sensitive', $field_definition->getSetting('case_sensitive'));
    $properties['postCode'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('postCode'))
      ->setSetting('case_sensitive', $field_definition->getSetting('case_sensitive'));
    $properties['city'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('city'))
      ->setSetting('case_sensitive', $field_definition->getSetting('case_sensitive'));
    $properties['countryCode'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('countryCode'))
      ->setSetting('case_sensitive', $field_definition->getSetting('case_sensitive'));
    $properties['phonePrivate'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('phonePrivate'))
      ->setSetting('case_sensitive', $field_definition->getSetting('case_sensitive'));
    $properties['email'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('email'))
      ->setSetting('case_sensitive', $field_definition->getSetting('case_sensitive'));
    $properties['birthday'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('birthday'))
      ->setSetting('case_sensitive', $field_definition->getSetting('case_sensitive'));
    $properties['birthmonth'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('birthmonth'))
      ->setSetting('case_sensitive', $field_definition->getSetting('case_sensitive'));
    $properties['birthyear'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('birthyear'))
      ->setSetting('case_sensitive', $field_definition->getSetting('case_sensitive'));

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    $schema = [
      'columns' => [
        'salutation' => [
          'type' => 'varchar',
          'length' => 10,
          'not null' => FALSE,
        ],
        'jobTitleCode' => [
          'type' => 'varchar',
          'length' => 10,
          'not null' => FALSE,
        ],
        'initials' => [
          'type' => 'varchar',
          'length' => 10,
          'not null' => FALSE,
        ],
        'firstName' => [
          'type' => 'varchar',
          'length' => 100,
          'not null' => FALSE,
        ],
        'middleName' => [
          'type' => 'varchar',
          'length' => 100,
          'not null' => FALSE,
        ],
        'surname' => [
          'type' => 'varchar',
          'length' => 100,
          'not null' => FALSE,
        ],
        'companyName' => [
          'type' => 'varchar',
          'length' => 100,
          'not null' => FALSE,
        ],
        'street' => [
          'type' => 'varchar',
          'length' => 100,
          'not null' => FALSE,
        ],
        'houseNo' => [
          'type' => 'varchar',
          'length' => 10,
          'not null' => FALSE,
        ],
        'postCode' => [
          'type' => 'varchar',
          'length' => 10,
          'not null' => FALSE,
        ],
        'city' => [
          'type' => 'varchar',
          'length' => 100,
          'not null' => FALSE,
        ],
        'countryCode' => [
          'type' => 'varchar',
          'length' => 20,
          'not null' => FALSE,
        ],
        'phonePrivate' => [
          'type' => 'varchar',
          'length' => 50,
          'not null' => FALSE,
        ],
        'email' => [
          'type' => 'varchar',
          'length' => Email::EMAIL_MAX_LENGTH,
          'not null' => FALSE,
        ],
        'birthday' => [
          'type' => 'varchar',
          'length' => 20,
          'not null' => FALSE,
        ],
        'birthmonth' => [
          'type' => 'varchar',
          'length' => 20,
          'not null' => FALSE,
        ],
        'birthyear' => [
          'type' => 'varchar',
          'length' => 20,
          'not null' => FALSE,
        ],
      ],
    ];

    return $schema;
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    $value = [
      $this->get('salutation')->getValue(),
      $this->get('jobTitleCode')->getValue(),
      $this->get('initials')->getValue(),
      $this->get('firstName')->getValue(),
      $this->get('middleName')->getValue(),
      $this->get('surname')->getValue(),
      $this->get('companyName')->getValue(),
      $this->get('street')->getValue(),
      $this->get('houseNo')->getValue(),
      $this->get('postCode')->getValue(),
      $this->get('city')->getValue(),
      $this->get('countryCode')->getValue(),
      $this->get('phonePrivate')->getValue(),
      $this->get('email')->getValue(),
      $this->get('birthday')->getValue(),
      $this->get('birthmonth')->getValue(),
      $this->get('birthyear')->getValue(),
    ];

    $empty = FALSE;

    foreach ($value as $item) {
      $empty &= empty($item);
    }

    return $empty;
  }

}
