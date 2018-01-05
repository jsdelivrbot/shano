<?php

namespace Drupal\child_sponsorship\Plugin\Field\FieldType;


use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\forms_suite\Plugin\Field\FieldType\FormsSuiteField;

/**
 * Plugin implementation of the 'wovi_child_field' field type.
 *
 * @FieldType(
 *   id = "wovi_child_field",
 *   label = @Translation("Child field"),
 *   description = @Translation("Forms suite child field"),
 *   default_widget = "wovi_child_widget",
 *   default_formatter = "wovi_formatter",
 *   category = @Translation("Forms suite")
 * )
 */
class WoViChildField extends FormsSuiteField
{

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition)
  {
    // Prevent early t() calls by using the TranslatableMarkup.
    $properties['childSequenceNo'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('childSequenceNo'))
      ->setSetting('case_sensitive', $field_definition->getSetting('case_sensitive'));
    $properties['childCountryCode'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('childCountryCode'))
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
        'childSequenceNo' => [
          'type' => 'varchar',
          'length' => 50,
          'not null' => FALSE,
        ],
        'childCountryCode' => [
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
      $this->get('childSequenceNo')->getValue(),
      $this->get('childCountryCode')->getValue(),
    ];
    $empty = FALSE;
    foreach ($value as $item) {
      $empty &= empty($item);
    }
    return $empty;
  }

}
