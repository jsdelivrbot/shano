<?php

/**
 * @file
 * Contains child.page.inc.
 *
 * Page callback for Child entities.
 */

use Drupal\Core\Render\Element;
use Drupal\Core\Link;
use Drupal\Core\Url;

/**
 * Prepares variables for Child templates.
 *
 * Default template: child.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_child(array &$variables) {
  // Fetch Child Entity Object.
  $child = $variables['elements']['#child'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
