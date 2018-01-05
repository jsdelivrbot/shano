<?php

namespace Drupal\forms_suite\Plugin\Field\FieldWidget;

use Drupal\Core\Entity\FieldableEntityInterface;
use Drupal\Core\Field\Plugin\Field\FieldWidget\OptionsSelectWidget;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'forms_select_widget' widget.
 *
 * @FieldWidget(
 *   id = "forms_select_widget",
 *   label = @Translation("Forms Select"),
 *   field_types = {
 *     "list_forms"
 *   }
 * )
 */
class FormsSelectWidget extends OptionsSelectWidget {
  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element = parent::formElement($items, $delta, $element, $form, $form_state);

    $element += array(
      '#type' => 'select',
      '#options' => $this->getOptions($items->getEntity()),
      '#default_value' => $this->getSelectedOptions($items),
      // Do not display a 'multiple' select box if there is only one option.
      '#multiple' => $this->multiple && count($this->options) > 1,
    );

    return $element;
  }

  protected function getOptions(FieldableEntityInterface $entity) {
    if (!isset($this->options)) {
      $options = [];

      $bundles = \Drupal::service('entity_type.bundle.info')
        ->getBundleInfo('forms_message');

      foreach ($bundles as $bundle => $info) {
        $options[$bundle] = $info['label'];
      }

      $this->options = $options;
    }

    return $this->options;
  }
}
