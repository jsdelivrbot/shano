<?php

/**
 * @file
 * Contains \Drupal\editorialfield\Plugin\Field\FieldFormatter\EditorialFormatter.
 */

namespace Drupal\editorialfield\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\Html;
use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Renderer;
use Drupal\Core\Template\Attribute;
use Drupal\layout_plugin\Layout;

/**
 * Plugin implementation of the 'editorial_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "editorial_formatter",
 *   label = @Translation("Editorial formatter"),
 *   field_types = {
 *     "editorial_field"
 *   }
 * )
 */
class EditorialFormatter extends FormatterBase {
  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return array(// Implement default settings.
      ) + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    return array(// Implement settings form.
      ) + parent::settingsForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    // Implement settings summary.

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $delta => $item) {
      $attributes = new Attribute([
        'class' => [
          'item-' . ($delta + 1),
        ],
      ]);

      if ($delta === 0) {
        $attributes->addClass('first');
      }
      elseif ($delta === count($items) - 1) {
        $attributes->addClass('last');
      }

      $element = $this->viewValue($item);

      \Drupal::moduleHandler()
        ->alter('editorial_formatter_layout_view', $element, $item);

      $element = NestedArray::mergeDeep($element, [
        '#attributes' => $attributes->toArray(),
      ]);

      $elements[$delta] = $element;
    }

    return $elements;
  }

  /**
   * Generate the output appropriate for one field item.
   *
   * @param \Drupal\Core\Field\FieldItemInterface $item
   *   One field item.
   *
   * @return string
   *   The textual output generated.
   */
  protected function viewValue(FieldItemInterface $item) {
    $build = [];

    if (!$item->isEmpty()) {
      $containner_attributes = new Attribute();

      // Initialize layout.
      $layout = Layout::layoutPluginManager()
        ->createInstance($item->layout, []);

      // Build region content.
      $region_content = [];
      foreach ($item->content as $region_name => $region_label) {
        $region_content[$region_name] = [];

        foreach ($item->content[$region_name] as $index => $content) {
          if (isset($content['entity_id']) && isset($content['entity_type'])) {
            $attributes = new Attribute([
              'class' => [
                'item-' . ($index + 1),
              ],
            ]);

            if ($index === 0) {
              $attributes->addClass('first');
            }
            elseif ($index === count($item->content[$region_name]) - 1) {
              $attributes->addClass('last');
            }
            if ($index === 0 && count($item->content[$region_name]) == 1) {
              $attributes->addClass('last');
            }


            $entity = \Drupal::entityTypeManager()
              ->getStorage($content['entity_type'])
              ->load($content['entity_id']);

            if (!$entity) {
              continue;
            }

            // @TODO Very specific code
//
//            if ($entity->bundle() == "story_teaser" && ($index === 0 && count($item->content[$region_name]) == 1)) {
//
//              if($region_name == "left"){
//                $st_counter += 1;
//                $last_entity_id = $content['entity_id'];
//
//                $attributes['class'][''] = "story-teaser-count-" . $st_counter;
//
//              }
//              $test = "";
//            }
//            if($region_name == "left" && $last_entity_id != $content['entity_id']) {
//              $st_counter = 0;
//              $test = "";
//
//            }


            $class = Html::cleanCssIdentifier('has--' . $entity->getEntityTypeId() . '--' . $entity->bundle());
            $containner_attributes->addClass($class);


            $entity_view = \Drupal::entityTypeManager()
              ->getViewBuilder($content['entity_type'])
              ->view($entity);

            \Drupal::moduleHandler()
              ->alter('editorialfield_entity_view', $entity, $item);


            $entity_view = NestedArray::mergeDeep($entity_view, [
              '#attributes' => $attributes->toArray(),
            ]);

            $region_content[$region_name][$index] = $entity_view;
          }
        }
      }

      // Build layout.
      $build = $layout->build($region_content);

      $build = NestedArray::mergeDeep($build, [
        '#attributes' => $containner_attributes->toArray(),
      ]);
    }

    return $build;
  }
}
