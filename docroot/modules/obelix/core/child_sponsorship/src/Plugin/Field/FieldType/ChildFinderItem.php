<?php

namespace Drupal\child_sponsorship\Plugin\Field\FieldType;

use Drupal\Component\Utility\Random;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element\Email;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\field\Entity\FieldConfig;

/**
 * Plugin implementation of the 'child_finder_item' field type.
 *
 * @FieldType(
 *   id = "child_finder_item",
 *   label = @Translation("child finder item"),
 *   description = @Translation("child finder item"),
 *   default_widget = "child_finder_widget",
 *   default_formatter = "child_finder_formatter",
 *   category = @Translation("World Vision")
 * )
 */
class ChildFinderItem extends FieldItemBase
{
  /**
   * {@inheritdoc}
   */
  public static function defaultStorageSettings()
  {
    return array(
      'case_sensitive' => FALSE,
    ) + parent::defaultStorageSettings();
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition)
  {
    // Prevent early t() calls by using the TranslatableMarkup.
    $properties['child_gender'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('child_gender'))
      ->setSetting('case_sensitive', $field_definition->getSetting('case_sensitive'));
    $properties['child_country'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('child_country'))
      ->setSetting('case_sensitive', $field_definition->getSetting('case_sensitive'));
    $properties['child_birthday'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('child_birthday'))
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
        'child_gender' => [
          'type' => 'varchar',
          'length' => 100,
          'not null' => FALSE,
        ],
        'child_country' => [
          'type' => 'varchar',
          'length' => 100,
          'not null' => FALSE,
        ],
        'child_birthday' => [
          'type' => 'varchar',
          'length' => 100,
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
      $this->get('child_gender')->getValue(),
      $this->get('child_country')->getValue(),
      $this->get('child_birthday')->getValue(),
    ];
    $empty = FALSE;
    foreach ($value as $item) {
      $empty &= empty($item);
    }
    return $empty;
  }

}
