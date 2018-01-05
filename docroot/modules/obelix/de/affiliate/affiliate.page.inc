<?php

/**
 * @file
 * Contains affiliate.page.inc.
 *
 * Page callback for Affiliate entities.
 */

use Drupal\Core\Render\Element;
use Drupal\Core\Link;
use Drupal\Core\Url;

/**
 * Prepares variables for Affiliate templates.
 *
 * Default template: affiliate.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_affiliate(array &$variables) {
  // Fetch AffiliateItem Entity Object.
  $affiliate = $variables['elements']['#affiliate'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}

/**
* Prepares variables for a custom entity type creation list templates.
*
* Default template: affiliate-content-add-list.html.twig.
*
* @param array $variables
*   An associative array containing:
*   - content: An array of affiliate-types.
*
* @see block_content_add_page()
*/
function template_preprocess_affiliate_content_add_list(&$variables) {
  $variables['types'] = array();
  $query = \Drupal::request()->query->all();
  foreach ($variables['content'] as $type) {
    $variables['types'][$type->id()] = array(
      'link' => Link::fromTextAndUrl($type->label(), new Url('entity.affiliate.add_form', array(
        'affiliate_type' => $type->id()
      ), array('query' => $query))),
      'description' => array(
      '#markup' => $type->label(),
      ),
      'title' => $type->label(),
      'localized_options' => array(
      'query' => $query,
      ),
    );
  }
}
