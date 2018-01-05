<?php

namespace Drupal\map\Plugin\Field\FieldType;

use Drupal\Component\Utility\Random;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Plugin implementation of the 'google_geo_object_field_type' field type.
 *
 * @FieldType(
 *   id = "google_geo_object_field_type",
 *   label = @Translation("Google geo object"),
 *   description = @Translation("Create Google Object with the draw library"),
 *   default_widget = "google_geo_object_widget_type",
 *   default_formatter = "google_geo_object_formatter_type"
 * )
 */
class GoogleGeoObjectFieldType extends FieldItemBase
{
    /**
     * {@inheritdoc}
     */
    public static function defaultStorageSettings()
    {
        return array(
            'is_ascii' => FALSE,
            'max_length' => 255,
            'case_sensitive' => FALSE,
        ) + parent::defaultStorageSettings();
    }

    /**
     * {@inheritdoc}
     */
    public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition)
    {
        // Prevent early t() calls by using the TranslatableMarkup.
        $properties['geo_json'] = DataDefinition::create('string')
            ->setLabel(new TranslatableMarkup('family name'))
            ->setSetting('case_sensitive', $field_definition->getSetting('case_sensitive'))
            ->setRequired(TRUE);
        return $properties;
    }

    /**
     * {@inheritdoc}
     */
    public static function schema(FieldStorageDefinitionInterface $field_definition)
    {
        $schema = array(
            'columns' => array(
                'geo_json' => array(
                    'type' => 'text',
                    'not null' => FALSE,
                    'size' => 'big',
                ),
            ),


        );

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
    public function isEmpty()
    {

        $value1 = $this->get('geo_json')->getValue();
        return empty($value1);
    }

}
