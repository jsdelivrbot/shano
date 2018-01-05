<?php

namespace Drupal\forms_suite\Plugin\Field\FieldType;

use Drupal\options\Plugin\Field\FieldType\ListStringItem;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'forms_item' field type.
 *
 * @FieldType(
 *   id = "list_forms",
 *   label = @Translation("Forms"),
 *   category = @Translation("Reference"),
 *   description = @Translation("Stores the reference to an forms type."),
 *   default_widget = "forms_select_widget",
 *   default_formatter = "forms_formatter"
 * )
 */
class ListFormsItem extends ListStringItem {
  public function storageSettingsForm(array &$form, FormStateInterface $form_state, $has_data) {
    return [];
  }
}
