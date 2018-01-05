<?php

/**
 * @file
 * Contains \Drupal\editorialfield\Plugin\Field\FieldType\EditorialField.
 */

namespace Drupal\editorialfield\Plugin\Field\FieldType;

use Drupal\Component\Utility\Random;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\TypedData\DataDefinitionInterface;
use Drupal\Core\TypedData\TypedDataInterface;
use Drupal\editorialfield\EditorialFieldConfigurationManager;

/**
 * Plugin implementation of the 'editorial_field' field type.
 *
 * @FieldType(
 *   id = "editorial_field",
 *   label = @Translation("Body"),
 *   category = @Translation("Editorial"),
 *   description = @Translation("This field stores information about multiple entity references for each region of a layout."),
 *   default_widget = "editorial_widget",
 *   default_formatter = "editorial_formatter"
 * )
 */
class EditorialField extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public function __construct(DataDefinitionInterface $definition, $name = NULL, TypedDataInterface $parent = NULL) {
    parent::__construct($definition, $name, $parent);
    $this->settings = \Drupal::moduleHandler()->invokeAll('editorial_field_settings_default');
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties = [];

    $properties['layout'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Layout'))
      ->setDescription(new TranslatableMarkup('The layout plugin definition.'))
      ->setRequired(TRUE);

    $properties['content'] = DataDefinition::create('any')
      ->setLabel(new TranslatableMarkup('Content'))
      ->setDescription(new TranslatableMarkup('An array of region content.'))
      ->setRequired(FALSE);

    $properties['settings'] = DataDefinition::create('any')
      ->setLabel(new TranslatableMarkup('Settings'))
      ->setDescription(new TranslatableMarkup('An array of additional settings.'))
      ->setRequired(FALSE);

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    $schema = array(
      'columns' => array(
        'layout' => array(
          'type' => 'varchar_ascii',
          'length' => 255,
        ),
        // @todo should this be an id to a relation rather than serialized data?!
        'content' => array(
          'type' => 'blob',
          'size' => 'big',
          'serialize' => TRUE,
        ),
        'settings' => array(
          'type' => 'blob',
          'size' => 'big',
          'serialize' => TRUE,
        ),
      ),
    );

    return $schema;
  }

  /**
   * {@inheritdoc}
   */
  public function getConstraints() {
    $constraints = parent::getConstraints();

//    if ($max_length = $this->getSetting('max_length')) {
//      $constraint_manager = \Drupal::typedDataManager()->getValidationConstraintManager();
//      $constraints[] = $constraint_manager->create('ComplexData', array(
//        'value' => array(
//          'Length' => array(
//            'max' => $max_length,
//            'maxMessage' => t('%name: may not be longer than @max characters.', array(
//              '%name' => $this->getFieldDefinition()->getLabel(),
//              '@max' => $max_length
//            )),
//          ),
//        ),
//      ));
//    }

    return $constraints;
  }

//  /**
//   * {@inheritdoc}
//   */
//  public static function generateSampleValue(FieldDefinitionInterface $field_definition) {
//    $random = new Random();
//    $values['value'] = $random->word(mt_rand(1, $field_definition->getSetting('max_length')));
//    return $values;
//  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    return empty($this->values['layout']);
  }
}
