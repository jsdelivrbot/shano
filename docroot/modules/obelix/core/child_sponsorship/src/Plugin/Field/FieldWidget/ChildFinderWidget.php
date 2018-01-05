<?php

namespace Drupal\child_sponsorship\Plugin\Field\FieldWidget;

use Drupal\child\Controller\ChildController;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\country\Controller\CountryController;


/**
 * Plugin implementation of the 'child_finder_widget' widget.
 *
 * @FieldWidget(
 *   id = "child_finder_widget",
 *   multiple_values = TRUE,
 *   label = @Translation("child finder"),
 *   field_types = {
 *     "child_finder_item"
 *   }
 * )
 */
class ChildFinderWidget extends WidgetBase
{
  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state)
  {
    $element = [];


    $element['child_gender'] = [
      '#type' => 'select',
      '#title' => t('Child gender'),
      '#description' => t('Select the child\'s gender.'),
      '#options' => [
        0 => t('random'),
      ]+ChildController::getGenderOptions(),
    ];

    $element['child_country'] = [
      '#type' => 'select',
      '#title' => t('Child country'),
      '#description' => t('Select the child\'s country.'),
      '#options' => [
          0 => t('random'),
        ]+CountryController::getCountryOptions(),
    ];

    $element['child_birthday'] = [
      '#type' => 'checkbox',
      '#title' => t('Child birthday'),
      '#description' => t('Select a child which has a birthday on the actually day.'),
    ];

    return $element;
  }
}
