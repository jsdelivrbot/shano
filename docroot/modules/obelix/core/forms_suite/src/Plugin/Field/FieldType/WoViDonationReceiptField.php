<?php

namespace Drupal\forms_suite\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Plugin implementation of the 'wovi_donation_receipt_field' field type.
 *
 * @FieldType(
 *   id = "wovi_donation_receipt_field",
 *   label = @Translation("Donation receipt field"),
 *   description = @Translation("Forms suite donation receipt field"),
 *   default_widget = "wovi_donation_receipt_widget",
 *   default_formatter = "wovi_formatter",
 *   category = @Translation("Forms suite")
 * )
 */
class WoViDonationReceiptField extends FormsSuiteField
{

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition)
  {
    // Prevent early t() calls by using the TranslatableMarkup.
    $properties['donationReceipt'] = DataDefinition::create('any')
      ->setLabel(new TranslatableMarkup('donationReceipt'))
      ->setSetting('case_sensitive', $field_definition->getSetting('case_sensitive'));
    $properties['adressnumber'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('adressnumber'))
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
        'donationReceipt' => [
          'type' => 'blob',
          'size' => 'big',
          'serialize' => TRUE,
        ],
        'adressnumber' => [
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
      $this->get('donationReceipt')->getValue(),
      $this->get('adressnumber')->getValue(),
    ];
    $empty = FALSE;
    foreach ($value as $item) {
      $empty &= empty($item);
    }
    return $empty;
  }

}
