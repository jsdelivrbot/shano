<?php

namespace Drupal\fs_payment\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\forms_suite\Plugin\Field\FieldType\FormsSuiteField;

/**
 * Plugin implementation of the 'wovi_yearly_donation_field' field type.
 *
 * @FieldType(
 *   id = "wovi_yearly_donation_field",
 *   label = @Translation("Yearly donation field"),
 *   description = @Translation("Forms suite yearly donation field"),
 *   default_widget = "wovi_yearly_donation_widget",
 *   default_formatter = "wovi_formatter",
 *   category = @Translation("Forms suite")
 * )
 */
class WoViYearlyDonationField extends FormsSuiteField
{
  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition)
  {
    // Prevent early t() calls by using the TranslatableMarkup.
    $properties['month13'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('month13'))
      ->setSetting('case_sensitive', $field_definition->getSetting('case_sensitive'))
      ->setRequired(TRUE);

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition)
  {
    $schema = [
      'columns' => [
        'month13' => [
          'type' => 'varchar',
          'length' => 50,
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
      $this->get('month13')->getValue(),
    ];
    $empty = FALSE;
    foreach ($value as $item) {
      $empty &= empty($item);
    }
    return $empty;
  }

}
