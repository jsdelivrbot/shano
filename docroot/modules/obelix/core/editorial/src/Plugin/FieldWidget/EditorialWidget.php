<?php

/**
 * @file
 * Contains \Drupal\editorial\Plugin\Field\FieldWidget\EditorialWidget.
 */

namespace Drupal\editorial\Plugin\Field\FieldWidget;

use Drupal\Component\Plugin\Exception\PluginNotFoundException;
use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;
use Drupal\editorial\EditorialFieldGlobalConfigurationManager;
use Drupal\layout_plugin\Layout;
use Drupal\layout_plugin\Plugin\Layout\LayoutDefault;
use Drupal\layout_plugin\Plugin\Layout\LayoutPluginManager;

/**
 * Plugin implementation of the 'editorial_widget' widget.
 *
 * @FieldWidget(
 *   id = "editorial_widget",
 *   label = @Translation("Editorial widget"),
 *   field_types = {
 *     "editorial_field"
 *   }
 * )
 */
class EditorialWidget extends EditorialWidgetBase {

  /**
   * @inheritdoc
   */
  public function buildLayoutElements(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $field_name = $this->fieldDefinition->getName();
    $values = $items[$delta]->getValue();

    // Default button properties.
    $button = [
      '#type' => 'submit',
      '#limit_validation_errors' => [
        array_merge($form['#parents'], [$field_name]),
      ],
      '#submit' => [
        [get_class($this), 'widgetMultistepSubmit'],
      ],
      '#field_name' => $field_name,
      '#value_parents' => [
        $delta,
      ],
    ];

    if ($layout_id = NestedArray::getValue($values, ['layout'])) {
      $element['layout'] = [
        '#type' => 'hidden',
        '#value' => $layout_id,
      ];

      $element['actions']['settings'] = $button + [
          '#value' => $this->t('Settings'),
          '#op' => 'display_item_settings_form',
          '#name' => implode('_', [
            $field_name,
            $delta,
            'settings',
          ]),
        ];
      $element['actions']['remove'] = $button + [
          '#button_type' => 'danger',
          '#value' => $this->t('Remove'),
          '#op' => 'display_item_remove_form',
          '#name' => implode('_', [
            $field_name,
            $delta,
            'remove',
          ]),
        ];
    }
    else {
      $element['layout'] = [
        '#type' => 'select',
        '#title' => $this->t('Layout'),
        '#options' => $this->getLayoutOptions(TRUE),
        '#empty_option' => '- ' . $this->t('Please select') . ' -',
      ];

      $element['actions']['submit'] = $button + [
          '#value' => $this->t('Insert layout'),
          '#op' => 'insert_item',
        ];
    }

    return $element;
  }

  public function buildItemContentElements(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $values = $items[$delta]->getValue();
    $content = NestedArray::getValue($values, ['content']);

    if ($layout_id = NestedArray::getValue($values, ['layout'])) {
      try {
        // Initialize layout.
        $layout = Layout::layoutPluginManager()
          ->createInstance($layout_id, []);

        // Build region content.
        $region_content = [];
        foreach ($layout->getRegionNames() as $region_name => $region_label) {
          $region_content[$region_name] = $this->buildLayoutRegionElements(
            $items,
            $delta,
            $layout,
            $region_name,
            isset($content[$region_name]) ? $content[$region_name] : [],
            $form, $form_state
          );
        }

        // Build layout.
        $element['content'] = $layout->build($region_content);
      } catch (PluginNotFoundException $e) {
        // Display warning and append raw content as value element to ensure it
        // won't get lost during next submission.
        $element['warning'] = [
          '#type' => 'markup',
          '#markup' => $this->t('Missing or broken layout.'),
        ];
      }
    }

    return $element;
  }

  public function buildLayoutRegionElements(FieldItemListInterface $items, $delta, LayoutDefault $layout, $region_name, array $entities, array &$form, FormStateInterface $form_state) {
    $field_name = $this->fieldDefinition->getName();
    $regions = $layout->getRegionNames();
    $max = count($entities);

    $elements = [];

    for ($index = 0; $index <= $max; $index++) {
      if (isset($entities[$index]['entity_id'])) {
        $entity = \Drupal::entityTypeManager()
          ->getStorage($entities[$index]['entity_type'])
          ->load($entities[$index]['entity_id']);

        $elements[$index] = $this->buildEntityElements($entity, $delta, $region_name, $index, $form, $form_state);
      }
      else {
        $mode = isset($entities[$index]['mode']) ? $entities[$index]['mode'] : NULL;
        switch ($mode) {
          case 'display_content_create_form':
            $entity = \Drupal::entityTypeManager()
              ->getStorage($entities[$index]['entity_type'])
              ->create([
                'type' => $entities[$index]['entity_bundle'],
              ]);
            $elements[$index] = $this->buildContentCreateElement($entity, $delta, $region_name, $index, $form, $form_state);
            break;
          case 'display_content_select_form':
            $entity = \Drupal::entityTypeManager()
              ->getStorage($entities[$index]['entity_type'])
              ->create([
                'type' => $entities[$index]['entity_bundle'],
              ]);
            $elements[$index] = $this->buildContentSelectElement($entity, $delta, $region_name, $index, $form, $form_state);
            break;
          default:
            $entity_type_options = EditorialFieldGlobalConfigurationManager::getEntityOptions(TRUE);

            // Default button properties.
            $button = [
              '#type' => 'submit',
//              '#dropbutton' => 'create',
              '#limit_validation_errors' => [
                array_merge($form['#parents'], [$field_name])
              ],
              '#submit' => [
                [get_class($this), 'widgetMultistepSubmit'],
              ],
              '#field_name' => $field_name,
              '#value_parents' => [
                $delta,
                'content',
                $region_name,
                $index,
              ]
            ];

            $elements[$index] = [
              'entity_info' => [
                '#type' => 'select',
                '#title' => $this->t('Content type'),
                '#options' => $this->getEntityBundleOptions(array_keys($entity_type_options)),
                '#empty_option' => '- ' . $this->t('Please select') . ' -',
              ],
              'actions' => [
                '#type' => 'actions',
                'create' => $button + [
                    '#value' => $this->t('Create'),
                    '#name' => implode('_', [
                      $field_name,
                      $delta,
                      $region_name,
                      'create'
                    ]),
                    '#op' => 'display_content_create_form',
                  ],
                'select' => $button + [
                    '#value' => $this->t('Select'),
                    '#name' => implode('_', [
                      $field_name,
                      $delta,
                      $region_name,
                      'select'
                    ]),
                    '#op' => 'display_content_select_form',
                  ],
              ],
            ];
            break;
        }
      }

      $elements[$index]['_weight'] = [
        '#type' => 'weight',
        '#title' => $this->t('Weight for row @number', ['@number' => $index + 1]),
        '#title_display' => 'invisible',
        // Note: this 'delta' is the FAPI #type 'weight' element's property.
        '#delta' => $max,
        '#default_value' => isset($entities[$index]['_weight']) ? $entities[$index]['_weight'] : $index,
        '#weight' => 100,
      ];
    }

    $elements += [
      '#theme' => 'field_multiple_value_form__editorial_widget__layout_region',
      '#field_name' => $field_name,
      '#field_parents' => array_merge($form['#parents'], [$field_name]),
      '#cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
      '#cardinality_multiple' => TRUE,
      '#title' => $regions[$region_name],
      '#max_delta' => $max + 1,
    ];

    return $elements;
  }

  public function buildEntityElements(ContentEntityInterface $entity, $delta, $region_name, $index, array &$form, FormStateInterface $form_state) {
    $field_name = $this->fieldDefinition->getName();
    $parents = array_merge($form['#parents'], [
      $field_name,
      $delta,
      'content',
      $region_name,
      $index,
    ]);

    $elements = [];

    switch ($form_state->get($parents)) {
      case 'edit':
        $elements = $this->buildContentCreateElement($entity, $delta, $region_name, $index, $form, $form_state);
        break;
      case 'remove':
        $elements = $this->buildContentRemoveElements($entity, $delta, $region_name, $index, $form, $form_state);
        break;
      default:
        $elements['preview'] = \Drupal::entityTypeManager()
          ->getViewBuilder($entity->getEntityTypeId())
          ->view($entity);

        $elements['entity_type'] = [
          '#type' => 'hidden',
          '#value' => $entity->getEntityTypeId(),
        ];
        $elements['entity_bundle'] = [
          '#type' => 'hidden',
          '#value' => $entity->bundle(),
        ];
        $elements['entity_id'] = [
          '#type' => 'hidden',
          '#value' => $entity->id(),
        ];

        // Default button properties.
        $button = [
          '#type' => 'submit',
          '#limit_validation_errors' => [
            array_merge($form['#parents'], [$field_name]),
          ],
          '#submit' => [
            [get_class($this), 'widgetMultistepSubmit'],
          ],
          '#field_name' => $field_name,
          '#value_parents' => [
            $field_name,
            $delta,
            'content',
            $region_name,
            $index,
          ],
        ];

        $elements['actions'] = ['#type' => 'actions'];
        $elements['actions']['edit'] = $button + [
            '#value' => $this->t('Edit'),
            '#name' => implode('_', [
              $field_name,
              $delta,
              $region_name,
              $index,
              'edit',
            ]),
            '#op' => 'display_content_edit_form',
          ];
        break;
    }

    return $elements;
  }

  public function buildContentCreateElement(EntityInterface $entity, $delta, $region_name, $index, array &$form, FormStateInterface $form_state) {
    $field_name = $this->fieldDefinition->getName();
    $ief_element_class = '\Drupal\inline_entity_form\Element\InlineEntityForm';

    $element = [];

    $element['ief'] = [
      '#type' => 'inline_entity_form',
      '#entity_type' => $entity->getEntityTypeId(),
      '#bundle' => $entity->bundle(),
      '#default_value' => $entity,
      '#process' => [
        [$ief_element_class, 'processEntityForm'],
      ],
      '#element_validate' => [
        [$ief_element_class, 'validateEntityForm'],
      ],
      '#ief_element_submit' => [
        [$ief_element_class, 'submitEntityForm'],
      ],
    ];

    // Default button properties.
    $button = [
      '#type' => 'submit',
      '#limit_validation_errors' => [
        array_merge($form['#parents'], [$field_name]),
      ],
      '#submit' => [
        [get_class($this), 'widgetMultistepSubmit'],
      ],
      '#field_name' => $field_name,
      '#value_parents' => [
        $delta,
        'content',
        $region_name,
        $index,
      ],
    ];

    $element['actions'] = ['#type' => 'actions'];
    $element['actions']['submit'] = $button + [
        '#button_type' => 'primary',
        '#value' => $this->t('Create'),
        '#op' => 'create_content',
      ];
    $element['actions']['cancel'] = $button + [
        '#value' => $this->t('Cancel'),
        '#op' => 'display_content_default_form',
        '#weight' => 50,
      ];

    if (!$entity->isNew()) {
      $element['actions']['submit']['#value'] = $this->t('Update');
      $element['actions']['remove'] = $button + [
          '#button_type' => 'danger',
          '#value' => $this->t('Remove'),
          '#name' => implode('_', [
            $field_name,
            $delta,
            $region_name,
            $index,
            'remove',
          ]),
          '#op' => 'display_content_remove_form',
        ];
    }

    return $element;
  }

  public function buildContentSelectElement(EntityInterface $entity, $delta, $region_name, $index, array &$form, FormStateInterface $form_state) {
    $field_name = $this->fieldDefinition->getName();

    $element = [];
    $element['entity_type'] = [
      '#type' => 'hidden',
      '#value' => $entity->getEntityTypeId(),
    ];
    $element['entity_bundle'] = [
      '#type' => 'hidden',
      '#value' => $entity->bundle(),
    ];
    $element['entity_id'] = [
      '#type' => 'entity_autocomplete',
      '#target_type' => $entity->getEntityTypeId(),
      '#autocreate' => FALSE,
    ];

    // Default button properties.
    $button = [
      '#type' => 'submit',
      '#limit_validation_errors' => [
        array_merge($form['#parents'], [$field_name]),
      ],
      '#submit' => [
        [get_class($this), 'widgetMultistepSubmit'],
      ],
      '#field_name' => $field_name,
      '#value_parents' => [
        $delta,
        'content',
        $region_name,
        $index,
      ],
    ];

    $element['actions'] = ['#type' => 'actions'];
    $element['actions']['submit'] = $button + [
        '#button_type' => 'primary',
        '#value' => t('Insert'),
        '#op' => 'insert_content',
      ];
    $element['actions']['cancel'] = $button + [
        '#value' => $this->t('Cancel'),
        '#op' => 'display_content_default_form',
      ];

    return $element;
  }

  public function buildItemSettingsForm(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $values = $items[$delta]->getValue();
    $field_name = $this->fieldDefinition->getName();

    $element['layout'] = [
      '#type' => 'select',
      '#title' => $this->t('Change layout'),
      '#disabled' => TRUE,
      '#options' => $this->getLayoutOptions(TRUE),
      '#default_value' => NestedArray::getValue($values, ['layout']),
      '#required' => TRUE,
    ];

    $element['settings'] = [
      '#type' => 'container',
      '#attribtues' => [],
    ];

    \Drupal::moduleHandler()->alter('editorial_widget_layout_settings_form', $element['settings'], $form_state, $items[$delta]);

    // Default button properties.
    $button = [
      '#type' => 'submit',
      '#limit_validation_errors' => [
        array_merge($form['#parents'], [$field_name]),
      ],
      '#submit' => [
        [get_class($this), 'widgetMultistepSubmit'],
      ],
      '#field_name' => $field_name,
      '#value_parents' => [
        $delta,
      ],
    ];

    $element['actions']['submit'] = $button + [
        '#value' => $this->t('Update'),
        '#button_type' => 'primary',
        '#op' => 'update_item',
      ];
    $element['actions']['cancel'] = $button + [
        '#value' => $this->t('Cancel'),
        '#op' => 'display_item_default_form',
      ];

    return $element;
  }
}
