<?php

namespace Drupal\forms_survey\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'wovi_survey_widget' widget.
 *
 * @FieldWidget(
 *   id = "wovi_survey_widget",
 *   multiple_values = TRUE,
 *   label = @Translation("wovi survey widget"),
 *   field_types = {
 *     "wovi_survey_field"
 *   }
 * )
 */
class WoViSurveyWidget extends WidgetBase
{

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state)
  {
    $element = [];

    $survey_options = [
      0 => t('Please choose'),
      403850 => t('TV-Spot'),
      'banner' => t('Online advertising (for example, banner, video, etc.)'),
      'social network' => t('Social networks (e.g. Facebook)'),
      403851 => t('Ad in a magazine'),
      403852 => t('Flyer in a magazine'),
      403853 => t('Recommendation (family, friends, colleagues)'),
      403854 => t('Information booth'),
      403855 => t('Call from World Vision'),
      403856 => t('Report on World Vision (as of press or media)'),
      403857 => t('Event (exhibition, seminar, event)'),
      403858 => t('Radio-Spot'),
      404575 => t('Billboard'),
      'other' => t('Other reasons'),
    ];
    if (forms_suite_is_child_sponsorship_english_form()) {
      $survey_options = [
        0 => 'Please select',
        403850 => 'TV commercial',
        'banner' => 'Online ad (for example, banner, video, etc.)',
        'social network' => 'Social networks (for example, Facebook or similar)',
        403851 => 'Advertisement in a magazine ',
        403852 => 'Supplement (Flyer) in a magazine',
        403853 => 'Recommendation (family, friends, colleagues)',
        403854 => 'Information stand',
        403855 => 'Call from World Vision',
        403856 => 'Report on World Vision (for example, in the press or other media)',
        403857 => 'Event (trade fair, seminar, other event)',
        403858 => 'Radio commercial',
        404575 => 'Billboard',
        'other' => 'Other',
      ];
    }
    $element['survey'] = [
      '#type' => 'select',
      '#label_display' => 'invisible',
      '#default_value' => 0,
      '#options' => $survey_options,
    ];

    $element['alternative'] = [
      '#type' => 'textfield',
      '#title' => t('Your point of contact with World Vision...'),
      '#states' => [
        'visible' => [':input[name="field_wovi_survey[survey]"]' => ['value' => "other"]],
      ],
    ];

    if (!empty($this->getFieldSetting('headline'))) {
      $element['headline'] = [
        '#markup' => t($this->getFieldSetting('headline')),
      ];
    }
    if (!empty($this->getFieldSetting('subline'))) {
      $element['subline'] = [
        '#markup' => t($this->getFieldSetting('subline')),
      ];
    }

    return $element;
  }

  public function form(FieldItemListInterface $items, array &$form, FormStateInterface $form_state, $get_delta = NULL)
  {
    $form_parent = parent::form($items, $form, $form_state, $get_delta);
    $form_parent['widget']['#theme'] = 'form_section__wovi_survey';
    $form_parent['widget']['#elements'] = $form_parent['widget'];

    return $form_parent;
  }


  /**
   * Form validation handler for widget elements.
   *
   * @param array $element
   *   The form element.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   */
  public static function validateElement(array $element, FormStateInterface &$form_state)
  {
  }

  /**
   * Ajax callback to validate the donation form.
   */
  public static function validateFormAjax(array &$form, FormStateInterface $form_state)
  {
  }

}
