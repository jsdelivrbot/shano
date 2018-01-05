<?php

namespace Drupal\forms_suite\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;
/**
 * Plugin implementation of the 'wovi_company_cooperation_field' field type.
 *
 * @FieldType(
 *   id = "wovi_company_cooperation_field",
 *   label = @Translation("Company cooperation field"),
 *   description = @Translation("Forms suite company cooperation field"),
 *   default_widget = "wovi_company_cooperation_widget",
 *   default_formatter = "wovi_formatter",
 *   category = @Translation("Forms suite")
 * )
 */
class WoViCompanyCooperationField extends FormsSuiteField
{

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition)
  {
    // Prevent early t() calls by using the TranslatableMarkup.
    $properties['companyCooperationCategory'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('companyCooperationCategory'))
      ->setSetting('case_sensitive', $field_definition->getSetting('case_sensitive'));

    $properties['companyCooperationSpecial'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('companyCooperationSpecial'))
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
        'companyCooperationCategory' => [
          'type' => 'varchar',
          'length' => 50,
          'not null' => FALSE,
        ],
        'companyCooperationSpecial' => [
          'type' => 'varchar',
          'length' => 250,
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
      $this->get('companyCooperationCategory')->getValue(),
      $this->get('companyCooperationSpecial')->getValue(),
    ];
    $empty = FALSE;
    foreach ($value as $item) {
      $empty &= empty($item);
    }
    return $empty;
  }

}
