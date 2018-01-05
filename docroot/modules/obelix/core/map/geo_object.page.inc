<?php

/**
 * @file
 * Contains geo_object.page.inc.
 *
 * Page callback for Geo object entities.
 */

use Drupal\Core\Render\Element;
use Drupal\Core\Link;
use Drupal\Core\Url;

/**
 * Prepares variables for Geo object templates.
 *
 * Default template: geo_object.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_geo_object(array &$variables) {
  // Fetch GeoObject Entity Object.
  $geo_object = $variables['elements']['#geo_object'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
