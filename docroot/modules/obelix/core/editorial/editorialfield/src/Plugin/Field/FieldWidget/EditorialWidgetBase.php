<?php

/**
 * @file
 * Contains \Drupal\editorialfield\Plugin\Field\FieldWidget\EditorialWidgetBase.
 */

namespace Drupal\editorialfield\Plugin\Field\FieldWidget;

use Drupal\Component\Serialization\Json;
use Drupal\Component\Utility\Html;
use Drupal\Component\Utility\NestedArray;
use Drupal\Component\Utility\SortArray;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Field\FieldFilteredMarkup;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormState;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormSubmitter;
use Drupal\editorialfield\EditorialFieldConfigurationManager;
use Drupal\editorialfield\Plugin\Field\FieldType\EditorialField;
use Drupal\forms_suite\Entity\Form;
use Drupal\inline_entity_form\ElementSubmit;

abstract class EditorialWidgetBase extends WidgetBase {
  /**
   * {@inheritdoc}
   */
  public function form(FieldItemListInterface $items, array &$form, FormStateInterface $form_state, $get_delta = NULL) {
    $field_name = $this->fieldDefinition->getName();
    $field_parents = $form['#parents'];

    // Create a custom widget state.
    if (!static::getWidgetState($field_parents, $field_name, $form_state)) {
      $field_state = [
        'items' => [],
        'array_parents' => [],
      ];

      // Load current items.
      foreach ($items as $delta => $item) {
        $field_state['items'][$delta] = $item->getValue();
      }

      static::setWidgetState($field_parents, $field_name, $form_state, $field_state);
    }

    // Collect widget elements.
    $elements = $this->formMultipleElements($items, $form, $form_state);

    // Populate the 'array_parents' information in $form_state->get('field')
    // after the form is built, so that we catch changes in the form structure
    // performed in alter() hooks.
    $elements['#after_build'][] = [get_class($this), 'afterBuild'];
    $elements['#field_name'] = $field_name;
    $elements['#field_parents'] = $field_parents;
    // Enforce the structure of submitted values.
    $elements['#parents'] = array_merge($field_parents, [$field_name]);
    // Most widgets need their internal structure preserved in submitted values.
    $elements += ['#tree' => TRUE];

    return array(
      // Aid in theming of widgets by rendering a classified container.
      '#type' => 'container',
      // Assign a different parent, to keep the main id for the widget itself.
      '#parents' => array_merge($field_parents, array($field_name . '_wrapper')),
      '#attributes' => array(
        'class' => array(
          'field--type-' . Html::getClass($this->fieldDefinition->getType()),
          'field--name-' . Html::getClass($field_name),
          'field--widget-' . Html::getClass($this->getPluginId()),
        ),
      ),
      'widget' => $elements,
    );
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $parents = array_merge($form['#parents'], [
      $this->fieldDefinition->getName(),
      $delta
    ]);

    switch ($form_state->get($parents)) {
      case 'settings':
        $element = $this->buildItemSettingsForm($items, $delta, $element, $form, $form_state);
        break;
      case 'remove':
        $element = $this->buildItemRemoveForm($items, $delta, $element, $form, $form_state);
        break;
      default:
        $element = $this->buildLayoutElements($items, $delta, $element, $form, $form_state);
        $element = $this->buildItemContentElements($items, $delta, $element, $form, $form_state);

        $element['settings'] = [
          '#type' => 'value',
          '#value' => $items[$delta]->settings,
        ];
        break;
    }

    $element['actions'] += [
      '#type' => 'actions',
      '#weight' => 99,
    ];
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function formMultipleElements(FieldItemListInterface $items, array &$form, FormStateInterface $form_state) {
    $field_name = $this->fieldDefinition->getName();
    $cardinality = $this->fieldDefinition->getFieldStorageDefinition()
      ->getCardinality();
    $field_parents = $form['#parents'];

    // Load current field state.
    $field_state = static::getWidgetState($field_parents, $field_name, $form_state);

    if (isset($field_state['items'])) {
//      $field_state['items'] = self::filterItemValues($field_state['items']);
      $items->setValue($field_state['items']);
    }

    $items->filterEmptyItems();

    // Determine the number of widgets to display.
    switch ($cardinality) {
      case FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED:
        $max = count($items);
        break;
      default:
        $max = $cardinality - 1;
        break;
    }

    $title = $this->fieldDefinition->getLabel();
    $description = FieldFilteredMarkup::create(\Drupal::token()
      ->replace($this->fieldDefinition->getDescription()));

    $elements = [];

    for ($delta = 0; $delta <= $max; $delta++) {
      // Add a new empty item if it doesn't exist yet at this delta.
      if (!isset($items[$delta])) {
        $items->appendItem();
      }

      $element = [
        '#title' => $this->t('@title (value @number)', [
          '@title' => $title,
          '@number' => $delta + 1
        ]),
        '#title_display' => 'invisible',
        '#description' => '',
      ];

      $element += $this->formSingleElement($items, $delta, $element, $form, $form_state);

      $element['_weight'] = [
        '#type' => 'weight',
        '#title' => $this->t('Weight for row @number', ['@number' => $delta + 1]),
        '#title_display' => 'invisible',
        // Note: this 'delta' is the FAPI #type 'weight' element's property.
        '#delta' => $max,
        '#default_value' => isset($items[$delta]->_weight) ? $items[$delta]->_weight : $delta,
        '#weight' => 100,
      ];

      $elements[$delta] = $element;
    }

    if ($elements) {
      $elements += [
        '#theme' => 'field_multiple_value_form__editorial_widget',
        '#field_name' => $field_name,
        '#cardinality' => $cardinality,
        '#cardinality_multiple' => TRUE,
        '#required' => $this->fieldDefinition->isRequired(),
        '#title' => $title,
        '#description' => $description,
        '#max_delta' => $max + 1,
      ];
    }

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'layouts' => [],
      'entities' => [],
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $settings = array_filter($this->getSettings());

    $form['layouts'] = [
      '#type' => 'details',
      '#title' => t('Available Layouts'),
      '#description' => t('Select which layouts should be available for editorial fields, you can confine this selection for each field separately. No selection means all are available.'),
      '#open' => !empty($settings['layouts']),
      '#tree' => TRUE,
    ];

    $form['layouts']['definitions'] = [
      '#type' => 'checkboxes',
      '#options' => $this->getLayoutOptions(),
      '#default_value' => $settings['layouts']['definitions'],
    ];

    $form['entities'] = [
      '#type' => 'details',
      '#title' => 'Available content',
      '#description' => t('Select which content types should be available for editorial fields, you can confine this selection for each field separately. No selection means all are available.'),
      '#open' => !empty($settings['entities']),
      '#tree' => TRUE,
    ];

    foreach ($this->getEntityOptions() as $entity_type_id => $entity_label) {
      $form['entities'][$entity_type_id] = [
        '#type' => 'checkboxes',
        '#title' => $entity_label,
        '#options' => EditorialFieldConfigurationManager::getEntityBundleOptions($entity_type_id),
        '#default_value' => isset($settings['entities'][$entity_type_id]) ? $settings['entities'][$entity_type_id] : [],
      ];
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];

    $summary[] = t('Layouts: !layouts', [
      '!layouts' => implode(', ', $this->getLayoutOptions(TRUE)),
    ]);

    $bundles = [];
    foreach ($this->getEntityOptions(TRUE) as $entity_type_id => $entity_label) {
      foreach ($this->getEntityBundleOptions($entity_type_id, TRUE) as $bundle_id => $bundle_label) {
        $bundles[] = $entity_label . ' - ' . $bundle_label;
      }
    }

    $summary[] = t('Content types: !content', [
      '!content' => implode(', ', $bundles),
    ]);


    return $summary;
  }

  /**
   * @param bool $only_available
   * @return array
   */
  public function getLayoutOptions($only_available = FALSE) {
    $options = EditorialFieldConfigurationManager::getLayoutOptions(TRUE);
    $settings = $this->getSettings();

    if ($only_available && !empty($settings['layouts']['definitions'])) {
      $options = array_intersect_key($options, $settings['layouts']['definitions']);
    }

    return $options;
  }

  /**
   * @param bool $only_available
   * @return array
   */
  public function getEntityOptions($only_available = FALSE) {
    $options = EditorialFieldConfigurationManager::getEntityOptions(TRUE);
    $settings = $this->getSettings();

    if ($only_available && !empty($settings['entities'])) {
      $options = array_intersect_key($options, $settings['entities']);
    }

    return $options;
  }

  /**
   * @param $entity_type_ids
   * @param bool $only_available
   * @return array
   */
  public function getEntityBundleOptions($entity_type_ids, $only_available = FALSE) {
    $entity_type_options = EditorialFieldConfigurationManager::getEntityOptions($only_available);
    $settings = $this->getSettings();
    $options = [];

    $entity_type_ids = (array) $entity_type_ids;
    foreach ($entity_type_ids as $entity_type_id) {
      $entity_bundles = EditorialFieldConfigurationManager::getEntityBundleOptions($entity_type_id);
      $entity_type_label = (string) $entity_type_options[$entity_type_id];

      if ($only_available && !empty($settings['entities'][$entity_type_id])) {
        $entity_bundles = array_intersect_key($entity_bundles, $settings['entities'][$entity_type_id]);
      }

      foreach ($entity_bundles as $entity_bundle => $bundle_label) {
        // Since select options can only handle one value we need to serialize
        // our information and decode them later in our submithandler.
        //
        // @see EditroialWidgetBase::widgetMultistepSubmit()
        $value = Json::encode([
          'entity_type' => $entity_type_id,
          'entity_bundle' => $entity_bundle,
        ]);
        $options[$entity_type_label][$value] = $bundle_label;
      }
    }

    return (count($entity_type_ids) == 1) ? reset($options) : $options;
  }

  /**
   * @return array
   */
  public function getSettings() {
    $arguments = func_num_args() ? func_get_arg(0) : parent::getSettings();

    $settings = [];
    foreach ($arguments as $key => $value) {
      if (is_array($value)) {
        $value = $this->getSettings(array_filter($value));
      }
      $settings[$key] = $value;
    }

    return array_filter($settings);
  }

  public function buildItemRemoveForm(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $field_name = $this->fieldDefinition->getName();

    $element['question'] = [
      '#prefix' => '<strong>',
      '#markup' => $this->t('Do you really want to remove this item?'),
      '#suffix' => '</strong>',
    ];

    $button = [
      '#type' => 'submit',
      '#limit_validation_errors' => [
        array_merge($form['#parents'], [$field_name]),
      ],
      '#submit' => [
        [get_class($this), 'widgetMultistepSubmit'],
      ],
    ];

    $element['actions']['submit'] = $button + [
        '#type' => 'submit',
        '#button_type' => 'primary',
        '#value' => $this->t('Confirm'),
        '#op' => 'remove_item',
        '#value_parents' => [
          $delta,
        ],
        '#field_name' => $field_name,
        '#name' => implode('_', [
          $field_name,
          $delta,
          'remove',
        ]),
      ];

    $element['actions']['cancel'] = $button + [
        '#value' => $this->t('Cancel'),
        '#op' => 'display_item_default_form',
        '#value_parents' => [$delta],
        '#field_name' => $field_name,
        '#name' => implode('_', [
          $field_name,
          $delta,
          'cancel',
        ]),
      ];

    return $element;
  }

  public function buildContentRemoveElements(ContentEntityInterface $entity, $delta, $region_name, $index, array &$form, FormStateInterface $form_state) {
    $field_name = $this->fieldDefinition->getName();

    $element['question'] = [
      '#prefix' => '<strong>',
      '#markup' => $this->t('Do you really want to remove this piece of content?'),
      '#suffix' => '</strong>',
    ];

    $element['actions'] = ['#type' => 'actions'];

    $button = [
      '#type' => 'submit',
      '#limit_validation_errors' => [
        array_merge($form['#parents'], [$field_name]),
      ],
      '#submit' => [
        [get_class($this), 'widgetMultistepSubmit'],
      ],
    ];

    $element['actions']['submit'] = $button + [
        '#type' => 'submit',
        '#button_type' => 'primary',
        '#value' => $this->t('Confirm'),
        '#name' => implode('_', [
          $field_name,
          $delta,
          $region_name,
          $index,
          'remove',
        ]),
        '#op' => 'remove_content',
        '#value_parents' => [
          $delta,
          'content',
          $region_name,
          $index,
        ],
        '#field_name' => $field_name,
      ];

    $element['actions']['cancel'] = $button + [
        '#value' => $this->t('Cancel'),
        '#op' => 'display_content_default_form',
        '#value_parents' => [$delta],
        '#field_name' => $field_name,
        '#name' => implode('_', [
          $field_name,
          $delta,
          $region_name,
          $index,
          'cancel',
        ]),
      ];

    return $element;
  }

  public static function widgetMultistepSubmit(array $form, FormStateInterface $form_state) {
    $button = $form_state->getTriggeringElement();

    $form_state->cleanValues();

    $value_parents = array_merge($form['#parents'], [$button['#field_name']]);
    $submitted_values = NestedArray::getValue($form_state->getValues(), $value_parents);

    $field_state = static::getWidgetState($form['#parents'], $button['#field_name'], $form_state);

    switch ($button['#op']) {
      case 'insert_item':
        // Nothing to do here.
        break;
      case 'update_item':
        // Get content from field state and reassign it to item.
        if (!isset($submitted_values['content'])) {
          $content = NestedArray::getValue($field_state['items'], array_merge($button['#value_parents'], ['content']));
          NestedArray::setValue($submitted_values, array_merge($button['#value_parents'], ['content']), $content);
        }

        $form_state->set(array_slice($button['#parents'], 0, -2), NULL);
        break;
      case 'remove_item':
        NestedArray::unsetValue($submitted_values, $button['#value_parents']);
        $form_state->set(array_slice($button['#parents'], 0, -2), NULL);
        break;
      case 'display_item_settings_form':
        $form_state->set(array_slice($button['#parents'], 0, -2), 'settings');
        break;
      case 'display_item_remove_form':
        $form_state->set(array_slice($button['#parents'], 0, -2), 'remove');
        break;
      case 'display_item_default_form':
        // Load latest state of elements submitted values from field_state.
        $submitted_element = NestedArray::getValue($field_state['items'], $button['#value_parents']);
        NestedArray::setValue($submitted_values, $button['#value_parents'], $submitted_element);
        // Reset elements status flag.
        $form_state->set(array_slice($button['#parents'], 0, -2), NULL);
        break;
      case 'create_content':
        // Get ief element and trigger ief submit handler.
        $item = NestedArray::getValue($form, array_slice($button['#array_parents'], 0, -2));
        ElementSubmit::doSubmit($item['ief'], $form_state);

        // Get new entity information and set them as submitted values.
        $submitted_element = [
          'entity_type' => $item['ief']['#entity']->getEntityTypeId(),
          'entity_bundle' => $item['ief']['#entity']->getType(),
          'entity_id' => $item['ief']['#entity']->id(),
          '_weight' => $item['_weight']['#value'],
        ];

        NestedArray::setValue($submitted_values, $button['#value_parents'], $submitted_element);
        // Remove flags.
        $form_state->set(array_slice($button['#parents'], 0, -2), NULL);
        break;
      case 'insert_content':
        $submitted_element = NestedArray::getValue($submitted_values, $button['#value_parents']);

        if ($submitted_element['entity_id']) {
          NestedArray::setValue($submitted_values, $button['#value_parents'], $submitted_element);
        } else {
          NestedArray::unsetValue($submitted_values, $button['#value_parents']);
        }

        // Remove flags.
        $form_state->set(array_slice($button['#parents'], 0, -2), NULL);
        break;
      case 'remove_content':
        NestedArray::unsetValue($submitted_values, $button['#value_parents']);
        $form_state->set(array_slice($button['#parents'], 0, -2), NULL);
        break;
      case 'display_content_default_form':
        // Load latest state of elements submitted values from field_state.
        $submitted_element = NestedArray::getValue($field_state['items'], $button['#value_parents']);
        // if we return from unfinished content creation remove content.
        if ($submitted_element['entity_id'] === NULL) {
          $submitted_element = [];
        }
        NestedArray::setValue($submitted_values, $button['#value_parents'], $submitted_element);

        // Reset elements status flag.
        $form_state->set(array_slice($button['#parents'], 0, -2), NULL);
        break;
      case 'display_content_select_form':
      case 'display_content_create_form':
        $submitted_element = NestedArray::getValue($submitted_values, $button['#value_parents']);

        if ($entity_info = Json::decode($submitted_element['entity_info'])) {
          $submitted_element['entity_type'] = $entity_info['entity_type'];
          $submitted_element['entity_bundle'] = $entity_info['entity_bundle'];
          $submitted_element['entity_id'] = NULL;
          // @todo think about using forms tate set()
          $submitted_element['mode'] = $button['#op'];
        }
        else {
          $submitted_element = [];
        }

        NestedArray::setValue($submitted_values, $button['#value_parents'], $submitted_element);
        NestedArray::unsetValue($submitted_values, array_merge($button['#value_parents'], ['entity_info']));
        break;
      case 'display_content_edit_form':
        $form_state->set(array_slice($button['#parents'], 0, -2), 'edit');
        break;
      case 'display_content_remove_form':
        // Load latest state of submitted values from field_state and set flag to remove for this element.
        $submitted_element = NestedArray::getValue($field_state['items'], $button['#value_parents']);
        NestedArray::setValue($submitted_values, $button['#value_parents'], $submitted_element);
        $form_state->set(array_slice($button['#parents'], 0, -2), 'remove');
        break;
    }

    // Update $form_state values.
    NestedArray::setValue($form_state->getValues(), $value_parents, $submitted_values);

    // Update $field_state items.
    NestedArray::setValue($field_state['items'], [], $submitted_values);

    self::filterItemContent($field_state['items']);
    self::sortItemContent($field_state['items']);

    // Update $field_state in widget state.
    static::setWidgetState($form['#parents'], $button['#field_name'], $form_state, $field_state);

    // Reset $form_state user_input.
    //
    // During the form rebuild, formElement() will create field item widget
    // elements using re-indexed deltas, so clear out FormState::$input to
    // avoid a mismatch between old and new deltas. The rebuilt elements will
    // have #default_value set appropriately for the current state of the field,
    // so nothing is lost in doing this.
    NestedArray::setValue($form_state->getUserInput(), array_merge($form['#parents'], [$button['#field_name']]), NULL);


    $form_state->setRebuild(TRUE);
  }

  public function massageFormValues(array $values, array $form, FormStateInterface $form_state) {

    self::filterItemContent($values);
    self::sortItemContent($values, TRUE);

    return $values;
  }

//  private static function filterItemValues(array $values) {
//    foreach ($values as $delta => $item) {
//      if (empty($item['layout'])) {
//        unset($values[$delta]);
//      }
//      elseif (isset($item['content'])) {
//        foreach ($item['content'] as $region_name => $region_content) {
//          foreach ($region_content as $index => $content) {
//            if (isset($content['entity_info']) && empty($content['content_info'])) {
//              unset($values[$delta]['content'][$region_name][$index]);
//            }
//            unset($values[$delta]['content'][$region_name][$index]['_weight']);
//          }
//
//
////          $values[$delta]['content'][$region_name] = array_filter($region_content);
//        }
//      }
//    }
//
//    // Return $values with new indexes
//    return array_filter($values);
//  }

  private static function filterItemContent(array &$items) {
    foreach ($items as $delta => $item) {
      if (isset($item['content'])) {
        foreach ($item['content'] as $region_name => $region_content) {
          foreach ($region_content as $index => $content) {
            if (self::isItemContentEmpty($content)) {
              unset($items[$delta]['content'][$region_name][$index]);
            }
          }
        }
      }
    }
  }

  private static function isItemContentEmpty(array $content) {
    return !(isset($content['entity_type']) && $content['entity_bundle']) ||
    (isset($content['entity_info']) && empty($content['content_info']));
  }

  private static function sortItemContent(array &$items, $reset_weight = FALSE) {
    foreach ($items as $delta => $item) {
      if (isset($item['content'])) {
        foreach ($item['content'] as $region_name => $region_content) {
          usort($region_content, function ($a, $b) {
            return SortArray::sortByKeyInt($a, $b, '_weight');
          });
          if ($reset_weight) {
            foreach ($region_content as $index => $content) {
              unset($region_content[$index]['_weight']);
            }
          }
          $items[$delta]['content'][$region_name] = $region_content;
        }
      }
    }
  }

  public abstract function buildLayoutElements(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state);

  public abstract function buildItemContentElements(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state);

  public abstract function buildItemSettingsForm(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state);

  public abstract function buildEntityElements(ContentEntityInterface $entity, $delta, $region_name, $index, array &$form, FormStateInterface $form_state);
}
