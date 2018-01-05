<?php

/**
 * @file
 * Contains map.page.inc.
 *
 * Page callback for Map entities.
 */

use Drupal\Core\Render\Element;
use Drupal\Core\Link;
use Drupal\Core\Url;

/**
 * Prepares variables for Map templates.
 *
 * Default template: map.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_map(array &$variables) {
  // Fetch Map Entity Object.
  $map = $variables['elements']['#map'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
