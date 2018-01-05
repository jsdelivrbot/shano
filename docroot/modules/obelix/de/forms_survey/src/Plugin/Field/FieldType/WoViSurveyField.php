<?php

namespace Drupal\forms_survey\Plugin\Field\FieldType;

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
 * Plugin implementation of the 'wovi_survey_field' field type.
 *
 * @FieldType(
 *   id = "wovi_survey_field",
 *   label = @Translation("wovi survey field"),
 *   description = @Translation("WoVi survey fields"),
 *   default_widget = "wovi_survey_widget",
 *   default_formatter = "wovi_formatter",
 *   category = @Translation("World Vision")
 * )
 */
class WoViSurveyField extends FieldItemBase
{
  /**
   * {@inheritdoc}
   */
  public static function defaultStorageSettings()
  {
    return array(
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
    $properties['survey'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('survey'))
      ->setSetting('case_sensitive', $field_definition->getSetting('case_sensitive'))
      ->setRequired(TRUE);
    $properties['alternative'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('alternative'))
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
        'survey' => [
          'type' => 'varchar',
          'length' => 100,
          'not null' => FALSE,
        ],
        'alternative' => [
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
      $this->get('survey')->getValue(),
      $this->get('alternative')->getValue(),
    ];
    $empty = FALSE;
    foreach ($value as $item) {
      $empty &= empty($item);
    }
    return $empty;
  }

}
