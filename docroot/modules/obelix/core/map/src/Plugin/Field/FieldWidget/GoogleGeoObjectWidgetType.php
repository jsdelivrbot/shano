<?php

namespace Drupal\map\Plugin\Field\FieldWidget;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'google_geo_object_widget_type' widget.
 *
 * @FieldWidget(
 *   id = "google_geo_object_widget_type",
 *   label = @Translation("Google geo object widget type"),
 *   field_types = {
 *     "google_geo_object_field_type"
 *   }
 * )
 */
class GoogleGeoObjectWidgetType extends WidgetBase
{
    /**
     * {@inheritdoc}
     */
    public static function defaultSettings()
    {
        return array(
            'placeholder' => '',
            'size' => 60,
        ) + parent::defaultSettings();
    }

    /**
     * {@inheritdoc}
     */
    public function settingsForm(array $form, FormStateInterface $form_state)
    {
        $elements = [];

        $elements['size'] = array(
            '#type' => 'number',
            '#title' => t('Size of textfield'),
            '#default_value' => $this->getSetting('size'),
            '#required' => TRUE,
            '#min' => 1,
        );
        $elements['placeholder'] = array(
            '#type' => 'textfield',
            '#title' => t('Placeholder'),
            '#default_value' => $this->getSetting('placeholder'),
            '#description' => t('Text that will be shown inside the field until a value is entered. This hint is usually a sample value or a brief description of the expected format.'),
        );

        return $elements;
    }

    /**
     * {@inheritdoc}
     */
    public function settingsSummary()
    {
        $summary = [];

        $summary[] = t('Textfield size: !size', array('!size' => $this->getSetting('size')));
        if (!empty($this->getSetting('placeholder'))) {
            $summary[] = t('Placeholder: @placeholder', array('@placeholder' => $this->getSetting('placeholder')));
        }

        return $summary;
    }

    /**
     * {@inheritdoc}
     */
    public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state)
    {

        $element = [];

        $element['#attached']['library'][] = 'map/google_geo_object_widget';
        $element['#attached']['drupalSettings']['map']['objects'] = (isset($items[$delta]->geo_json)) ? $items[$delta]->geo_json : NULL;

        $element['google_map'] = array(
            '#type' => 'item',
            '#title' => t('Set your object'),
            '#markup' => '<div id="map"></div>',
        );

        $element['geo_json'] = array(
            '#type' => 'hidden',
            '#default_value' => (isset($items[$delta]->geo_json)) ? $items[$delta]->geo_json : NULL,
            '#attributes' => [
                'id' => ['geo_json'],
            ]
        );

        return $element;
    }


}
