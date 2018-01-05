<?php

namespace Drupal\fs_payment\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\field\Entity\FieldConfig;
use Drupal\forms_suite\Plugin\Field\FieldType\FormsSuiteField;

/**
 * Plugin implementation of the 'wovi_payment_method_field' field type.
 *
 * @FieldType(
 *   id = "wovi_payment_method_field",
 *   label = @Translation("wovi payment method field"),
 *   description = @Translation("WoVi payment method fields"),
 *   default_widget = "wovi_payment_method_widget",
 *   default_formatter = "wovi_formatter",
 *   category = @Translation("World Vision")
 * )
 */
class WoViPaymentMethodField extends FormsSuiteField
{

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition)
  {
    // Prevent early t() calls by using the TranslatableMarkup.
    $properties['paymentMethod'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('paymentMethod'))
      ->setSetting('case_sensitive', $field_definition->getSetting('case_sensitive'));
    $properties['IBAN'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('IBAN'))
      ->setSetting('case_sensitive', $field_definition->getSetting('case_sensitive'));
    $properties['swiftCode'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('swiftCode'))
      ->setSetting('case_sensitive', $field_definition->getSetting('case_sensitive'));
    $properties['bankBranchNo'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('bankBranchNo'))
      ->setSetting('case_sensitive', $field_definition->getSetting('case_sensitive'));
    $properties['bankAccountNo'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('bankAccountNo'))
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
        'paymentMethod' => [
          'type' => 'varchar',
          'length' => 50,
          'not null' => FALSE,
        ],
        'IBAN' => [
          'type' => 'varchar',
          'length' => 50,
          'not null' => FALSE,
        ],
        'swiftCode' => [
          'type' => 'varchar',
          'length' => 50,
          'not null' => FALSE,
        ],
        'bankBranchNo' => [
          'type' => 'varchar',
          'length' => 50,
          'not null' => FALSE,
        ],
        'bankAccountNo' => [
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

    $element['forms_suite_configs']['external_payment'] = [
      '#type' => 'checkbox',
      '#title' => t('External payment'),
      '#default_value' => ($forms_suite_configs['external_payment'] === NULL)
        ? 1 : $forms_suite_configs['external_payment'],
      '#description' => t('Enable external payment in the section.'),
    ];

    $element['forms_suite_configs']['optional_bic'] = [
      '#type' => 'checkbox',
      '#title' => t('BIC is optional'),
      '#default_value' => !isset($forms_suite_configs['optional_bic'])
        ? FALSE : $forms_suite_configs['optional_bic'],
      '#description' => t('User can omit BIC.'),
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty()
  {
    $value = [
      $this->get('paymentMethod')->getValue(),
      $this->get('IBAN')->getValue(),
      $this->get('swiftCode')->getValue(),
      $this->get('bankBranchNo')->getValue(),
      $this->get('bankAccountNo')->getValue(),
    ];

    $empty = FALSE;
    foreach ($value as $item) {
      $empty &= empty($item);
    }

    return $empty;
  }

}
