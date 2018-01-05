<?php

namespace Drupal\child_sponsorship\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\field\Entity\FieldConfig;
use Drupal\forms_suite\Plugin\Field\FieldType\FormsSuiteField;

/**
 * Plugin implementation of the 'wovi_suggestion_field' field type.
 *
 * @FieldType(
 *   id = "wovi_suggestion_field",
 *   label = @Translation("Suggestion field"),
 *   description = @Translation("Forms suite suggestion field"),
 *   default_widget = "wovi_suggestion_widget",
 *   default_formatter = "wovi_formatter",
 *   category = @Translation("Forms suite")
 * )
 */
class WoViSuggestionField extends FormsSuiteField
{

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition)
  {
    // Prevent early t() calls by using the TranslatableMarkup.
    $properties['suggestion'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('suggestion'))
      ->setSetting('case_sensitive', $field_definition->getSetting('case_sensitive'))
      ->setRequired(TRUE);
    $properties['childSequenceNo'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('childCountryCode'))
      ->setSetting('case_sensitive', $field_definition->getSetting('case_sensitive'))
      ->setRequired(TRUE);
    $properties['childCountryCode'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('childCountryCode'))
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
        'suggestion' => [
          'type' => 'varchar',
          'length' => 10,
          'not null' => FALSE,
        ],
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
  public function fieldSettingsForm(array $form, FormStateInterface $form_state)
  {
    $element = parent::fieldSettingsForm($form, $form_state);
    /** @var FieldConfig $field */
    $field = $form_state->getFormObject()->getEntity();

    $forms_suite_configs = $field->getSetting('forms_suite_configs');

    $element['forms_suite_configs']['suggestion_type'] = [
      '#type' => 'radios',
      '#title' => t('Suggestion'),
      '#default_value' => ($forms_suite_configs['suggestion_type'] === NULL) ? 1 : $forms_suite_configs['suggestion_type'],
      '#options' => [
        1 => t('Suggestion via e-mail'),
        2 => t('Suggestion via post'),
      ],
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty()
  {
    $value = [
      $this->get('suggestion')->getValue(),
      $this->get('childCountryCode')->getValue(),
      $this->get('childSequenceNo')->getValue(),
    ];
    $empty = FALSE;
    foreach ($value as $item) {
      $empty &= empty($item);
    }
    return $empty;
  }

}
