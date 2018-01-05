<?php

/**
 * @file
 * Contains editorial_content.page.inc.
 *
 * Page callback for Editorial content entities.
 */

use Drupal\Core\Render\Element;
use Drupal\Core\Link;
use Drupal\Core\Url;

/**
 * Prepares variables for Editorial content templates.
 *
 * Default template: editorial_content.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_editorial_content(array &$variables) {
  // Fetch EditorialContent Entity Object.
  $editorial_content = $variables['elements']['#editorial_content'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
  // Make EntityId available in template-files
  $variables['id'] = $editorial_content->id();
}

/**
* Prepares variables for a custom entity type creation list templates.
*
* Default template: editorial_content-content-add-list.html.twig.
*
* @param array $variables
*   An associative array containing:
*   - content: An array of editorial_content-types.
*
* @see block_content_add_page()
*/
function template_preprocess_editorial_content_content_add_list(&$variables) {
  $variables['types'] = array();
  $query = \Drupal::request()->query->all();
  foreach ($variables['content'] as $type) {
    $variables['types'][$type->id()] = array(
      'link' => Link::fromTextAndUrl($type->label(), new Url('entity.editorial_content.add_form', array(
        'editorial_content_type' => $type->id()
      ), array('query' => $query))),
      'description' => array(
        '#markup' => $type->getDescription(),
      ),
      'label' => $type->label(),
      'localized_options' => array(
        'query' => $query,
      ),
    );
  }
}
