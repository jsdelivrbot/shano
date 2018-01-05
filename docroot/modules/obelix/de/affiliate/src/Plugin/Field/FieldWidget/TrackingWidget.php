<?php

namespace Drupal\affiliate\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'tracking_widget' widget.
 *
 * @FieldWidget(
 *   id = "tracking_widget",
 *   label = @Translation("Affiliate codes"),
 *   field_types = {
 *     "tracking"
 *   }
 * )
 */
class TrackingWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'exclude' => [],
    ] + parent::defaultSettings();
  }

  /**
   * Gets the excluded input elements as options array.
   *
   * @param array $filter
   *   An array of machine names to filter the options array.
   *
   * @return array An array of input element labels keyed by the machine name.
   * An array of input element labels keyed by the machine name.
   */
  private function getExcludeOptions(array $filter = []) {
    $options = [
      'motivation_code' => $this->t('Motivation Code'),
      'designation_code' => $this->t('Designation Code'),
      'additional_tracking' => $this->t('Additional tracking'),
    ];

    if (!empty($filter)) {
      $options = array_intersect_key($options, $filter);
    }

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements = [];

    $elements['exclude'] = [
      '#type' => 'checkboxes',
      '#title' => t('Exclude from input'),
      '#options' => $this->getExcludeOptions(),
      '#default_value' => $this->getSetting('exclude'),
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];

    if ($exclude = array_filter($this->getSetting('exclude'))) {
      $summary[] = t('Excluded: @list', [
          '@list' => implode(', ', $this->getExcludeOptions($exclude))
        ]
      );
    }

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element += [
      '#type' => 'details',
      '#title' => $this->t('Tracking'),
      '#open' => !$items[$delta]->isEmpty(),
    ];

    $element['motivation_code'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Motivation code'),
      '#max' => 32,
      '#default_value' => isset($items[$delta]->motivation_code) ? $items[$delta]->motivation_code : NULL,
    ];

    $element['designation_code'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Designation code'),
      '#max' => 32,
      '#default_value' => isset($items[$delta]->designation_code) ? $items[$delta]->designation_code : NULL,
    ];

    $element['additional_tracking'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Additional Tracking'),
      '#default_value' => isset($items[$delta]->additional_tracking) ? $items[$delta]->additional_tracking : NULL,
    ];

    foreach ($this->getSetting('exclude') as $delta) {
      $element[$delta]['#type'] = 'value';
    }

    return $element;
  }
}
