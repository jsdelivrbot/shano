<?php

namespace Drupal\fs_payment\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\field\Entity\FieldConfig;
use Drupal\forms_suite\Plugin\Field\FieldType\FormsSuiteField;

/**
 * Plugin implementation of the 'wovi_donation_period_field' field type.
 *
 * @FieldType(
 *   id = "wovi_donation_period_field",
 *   label = @Translation("Donation period field"),
 *   description = @Translation("Forms suite donation period field"),
 *   default_widget = "wovi_donation_period_widget",
 *   default_formatter = "wovi_formatter",
 *   category = @Translation("Forms suite")
 * )
 */
class WoViDonationPeriodField extends FormsSuiteField
{

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition)
  {
    // Prevent early t() calls by using the TranslatableMarkup.
    $properties['billingPeriod'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('billingPeriod'))
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
        'billingPeriod' => [
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
  public function fieldSettingsForm(array $form, FormStateInterface $form_state)
  {
    $element = parent::fieldSettingsForm($form, $form_state);
    /** @var FieldConfig $field */
    $field = $form_state->getFormObject()->getEntity();
    $forms_suite_configs = $field->getSetting('forms_suite_configs');

    $element['forms_suite_configs']['period_method'] = [
      '#type' => 'checkboxes',
      '#title' => t('Donation period method'),
      '#options' => [
        'single_period' => 'Single period',
        'interval_period' => 'Interval period',
      ],
      '#default_value' => ($forms_suite_configs['period_method'] !== NULL ) ? $forms_suite_configs['period_method'] : [],
      '#description' => t('Enable the period method. If no checkbox is activated, both methods are made available to the user.'),
    ];
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty()
  {
    $value = [
      $this->get('billingPeriod')->getValue(),
    ];
    $empty = FALSE;
    foreach ($value as $item) {
      $empty &= empty($item);
    }
    return $empty;
  }

}
