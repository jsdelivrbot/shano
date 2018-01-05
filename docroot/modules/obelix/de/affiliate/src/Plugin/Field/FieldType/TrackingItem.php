<?php

namespace Drupal\affiliate\Plugin\Field\FieldType;

use Drupal\Component\Utility\Random;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Plugin implementation of the 'tracking' field type.
 *
 * @FieldType(
 *   id = "tracking",
 *   label = @Translation("Tracking"),
 *   category = @translation("Affiliate"),
 *   description = @Translation("Allows to override tracking parameters."),
 *   default_widget = "tracking_widget",
 *   default_formatter = "tracking_formatter"
 * )
 */
class TrackingItem extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties['motivation_code'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Motivation Code'));

    $properties['designation_code'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Designation Code'));

    $properties['additional_tracking'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Additional Tracking'));

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    $schema = [
      'columns' => [
        'motivation_code' => [
          'type' => 'varchar',
          'length' => 32,
        ],
        'designation_code' => [
          'type' => 'varchar',
          'length' => 32,
        ],
        'additional_tracking' => [
          'type' => 'text',
        ],
      ],
    ];

    return $schema;
  }

  /**
   * {@inheritdoc}
   */
  public static function generateSampleValue(FieldDefinitionInterface $field_definition) {
    $values['motivation_code'] = mt_rand(0);
    $values['designation_code'] = mt_rand(0);

    return $values;
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    $motivation_code = $this->get('motivation_code')->getValue();
    $designation_code = $this->get('designation_code')->getValue();
    $additional_tracking = $this->get('additional_tracking')->getValue();

    return ($motivation_code === NULL || $motivation_code === '') &&
    ($designation_code === NULL || $designation_code === '') &&
    ($additional_tracking === NULL || $additional_tracking === '');
  }

  /**
   * Gets the motivation code.
   *
   * @return integer|null
   *   The motivation code.
   */
  public function getMotivationCode() {
    return $this->get('motivation_code')->getValue();
  }

  /**
   * Gets the designation code.
   *
   * @return integer|null
   *   The designation code.
   */
  public function getDesignationCode() {
    return $this->get('designation_code')->getValue();
  }

  /**
   * Gets the additional tracking.
   *
   * @return string|null
   *   The additional tracking.
   */
  public function getAdditionalTracking() {
    return $this->get('additional_tracking')->getValue();
  }
}
