<?php

namespace Drupal\forms_suite\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Plugin implementation of the 'wovi_press_distributor_field' field type.
 *
 * @FieldType(
 *   id = "wovi_press_distributor_field",
 *   label = @Translation("Press distributor field"),
 *   description = @Translation("Forms suite press distributor field"),
 *   default_widget = "wovi_press_distributor_widget",
 *   default_formatter = "wovi_formatter",
 *   category = @Translation("Forms suite")
 * )
 */
class WoViPressDistributorField extends FormsSuiteField
{

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition)
  {
    // Prevent early t() calls by using the TranslatableMarkup.
    $properties['adressnumber'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('adressnumber'))
      ->setSetting('case_sensitive', $field_definition->getSetting('case_sensitive'));
    $properties['editorialStaff'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('editorialStaff'))
      ->setSetting('case_sensitive', $field_definition->getSetting('case_sensitive'));
    $properties['journalist'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('journalist'))
      ->setSetting('case_sensitive', $field_definition->getSetting('case_sensitive'));
    $properties['shippingMethod'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('shippingMethod'))
      ->setSetting('case_sensitive', $field_definition->getSetting('case_sensitive'));
    $properties['topics'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('topics'))
      ->setSetting('case_sensitive', $field_definition->getSetting('case_sensitive'));
    $properties['printed'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('printed'))
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
        'adressnumber' => [
          'type' => 'varchar',
          'length' => 100,
          'not null' => FALSE,
        ],
        'editorialStaff' => [
          'type' => 'varchar',
          'length' => 100,
          'not null' => FALSE,
        ],
        'journalist' => [
          'type' => 'varchar',
          'length' => 10,
          'not null' => FALSE,
        ],
        'shippingMethod' => [
          'type' => 'varchar',
          'length' => 10,
          'not null' => FALSE,
        ],
        'topics' => [
          'type' => 'varchar',
          'length' => 10,
          'not null' => FALSE,
        ],
        'printed' => [
          'type' => 'varchar',
          'length' => 10,
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
      $this->get('adressnumber')->getValue(),
      $this->get('editorialStaff')->getValue(),
      $this->get('journalist')->getValue(),
      $this->get('shippingMethod')->getValue(),
      $this->get('topics')->getValue(),
      $this->get('printed')->getValue(),
    ];
    $empty = FALSE;
    foreach ($value as $item) {
      $empty &= empty($item);
    }
    return $empty;
  }

}
