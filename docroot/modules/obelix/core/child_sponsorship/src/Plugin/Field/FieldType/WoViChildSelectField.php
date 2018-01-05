<?php

namespace Drupal\child_sponsorship\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\field\Entity\FieldConfig;
use Drupal\forms_suite\Plugin\Field\FieldType\FormsSuiteField;

/**
 * Plugin implementation of the 'wovi_child_select_field' field type.
 *
 * @FieldType(
 *   id = "wovi_child_select_field",
 *   label = @Translation("Child select field"),
 *   description = @Translation("Forms suite child select field"),
 *   default_widget = "wovi_child_select_widget",
 *   default_formatter = "wovi_formatter",
 *   category = @Translation("Forms suite")
 * )
 */
class WoViChildSelectField extends FormsSuiteField
{

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition)
  {
    // Prevent early t() calls by using the TranslatableMarkup.
    $properties['childGender'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('childGender'))
      ->setSetting('case_sensitive', $field_definition->getSetting('case_sensitive'));
    $properties['child_region'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('child_region'))
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
        'childGender' => [
          'type' => 'varchar',
          'length' => 20,
          'not null' => FALSE,
        ],
        'child_region' => [
          'type' => 'varchar',
          'length' => 100,
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
  public function fieldSettingsForm(array $form, FormStateInterface $form_state)
  {
    $element = parent::fieldSettingsForm($form, $form_state);
    /** @var FieldConfig $field */
    $field = $form_state->getFormObject()->getEntity();

    $forms_suite_configs = $field->getSetting('forms_suite_configs');

    $element['forms_suite_configs']['all_countries'] = [
      '#type' => 'checkbox',
      '#title' => t('All countrys'),
      '#default_value' => ($forms_suite_configs['all_countries'] === NULL) ? 0 : $forms_suite_configs['all_countries'],
      '#description' => t('Show all countries in the country list. Otherwise you will see only countries with non sponsored children.'),
    ];
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty()
  {
    $value = [
      $this->get('childGender')->getValue(),
      $this->get('child_region')->getValue(),
      $this->get('childCountryCode')->getValue(),
    ];
    $empty = FALSE;
    foreach ($value as $item) {
      $empty &= empty($item);
    }
    return $empty;
  }

}
