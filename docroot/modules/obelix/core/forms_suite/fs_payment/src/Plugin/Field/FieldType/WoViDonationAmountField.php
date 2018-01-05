<?php

namespace Drupal\fs_payment\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\field\Entity\FieldConfig;
use Drupal\forms_suite\Plugin\Field\FieldType\FormsSuiteField;

/**
 * Plugin implementation of the 'wovi_donation_amount_field' field type.
 *
 * @FieldType(
 *   id = "wovi_donation_amount_field",
 *   label = @Translation("Donation amount field"),
 *   description = @Translation("Forms suite donation amount field"),
 *   default_widget = "wovi_donation_amount_widget",
 *   default_formatter = "wovi_formatter",
 *   category = @Translation("Forms suite")
 * )
 */
class WoViDonationAmountField extends FormsSuiteField
{

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition)
  {
    // Prevent early t() calls by using the TranslatableMarkup.
    $properties['amount'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('amount'))
      ->setSetting('case_sensitive', $field_definition->getSetting('case_sensitive'))
      ->setRequired(TRUE);
    $properties['amountRadio'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('amountRadio'))
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
        'amount' => [
          'type' => 'varchar',
          'length' => 50,
          'not null' => FALSE,
        ],
        'amountRadio' => [
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

    $element['forms_suite_configs']['single_amount'] = [
      '#type' => 'checkbox',
      '#title' => t('Display single amount field.'),
      '#default_value' => ($forms_suite_configs['single_amount'] !== NULL ) ? $forms_suite_configs['single_amount'] : 0,
      '#description' => t('Enable only the single amount field. Unchecked it will also display three radio buttons.'),
    ];
    $element['forms_suite_configs']['minimal_amount'] = [
      '#type' => 'number',
      '#title' => t('Minimal amount'),
      '#default_value' => $forms_suite_configs['minimal_amount'] === NULL ? 5 : $forms_suite_configs['minimal_amount'],
      '#description' => t('Set the minimal donation amount.'),
    ];
    $element['forms_suite_configs']['default_amount'] = [
      '#type' => 'number',
      '#title' => t('Default amount'),
      '#default_value' => $forms_suite_configs['default_amount'] === NULL ? 5 : $forms_suite_configs['default_amount'],
      '#description' => t('Set the default donation amount.'),
    ];
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty()
  {
    $value = [
      $this->get('amount')->getValue(),
      $this->get('amountRadio')->getValue(),
    ];
    $empty = FALSE;
    foreach ($value as $item) {
      $empty &= empty($item);
    }
    return $empty;
  }

}
