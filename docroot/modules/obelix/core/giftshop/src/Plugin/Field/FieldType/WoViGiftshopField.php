<?php

namespace Drupal\giftshop\Plugin\Field\FieldType;

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
 * Plugin implementation of the 'wovi_giftshop_field' field type.
 *
 * @FieldType(
 *   id = "wovi_giftshop_field",
 *   label = @Translation("wovi giftshop field"),
 *   description = @Translation("WoVi giftshop fields"),
 *   default_widget = "wovi_giftshop_widget",
 *   default_formatter = "wovi_formatter",
 *   category = @Translation("World Vision")
 * )
 */
class WoViGiftshopField extends FieldItemBase
{
  /**
   * {@inheritdoc}
   */
  public static function defaultStorageSettings()
  {
    return array(
      'is_ascii' => FALSE,
      'case_sensitive' => FALSE,
    ) + parent::defaultStorageSettings();
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultFieldSettings()
  {
    $settings = array(
        'headline' => NULL,
        'subline' => NULL,
      ) + parent::defaultFieldSettings();
    return $settings;
  }

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
    $properties['gifts'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('gifts'))
      ->setSetting('case_sensitive', $field_definition->getSetting('case_sensitive'))
      ->setRequired(TRUE);
    $properties['gift_price'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('gift_price'))
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
          'length' => 100,
          'not null' => FALSE,
        ],
        'gifts' => [
          'type' => 'blob',
          'size' => 'big',
          'serialize' => TRUE,
        ],
        'gift_price' => [
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
  public function getConstraints()
  {
    $constraints = parent::getConstraints();

    return $constraints;
  }

  /**
   * {@inheritdoc}
   */
  public function fieldSettingsForm(array $form, FormStateInterface $form_state)
  {
    $element = parent::fieldSettingsForm($form, $form_state);
    /** @var FieldConfig $field */
    $field = $form_state->getFormObject()->getEntity();


    $element['headline'] = [
      '#type' => 'textfield',
      '#title' => t('Section headline'),
      '#default_value' => $field->getSetting('headline'),
      '#description' => t('Write a headline over the form section.'),
    ];

    $element['subline'] = [
      '#type' => 'textfield',
      '#title' => t('Section subline'),
      '#default_value' => $field->getSetting('subline'),
      '#description' => t('Write a subline over the section.'),
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
      $this->get('gifts')->getValue(),
      $this->get('gift_price')->getValue(),
    ];
    $empty = FALSE;
    foreach ($value as $item) {
      $empty &= empty($item);
    }
    return $empty;
  }

}
