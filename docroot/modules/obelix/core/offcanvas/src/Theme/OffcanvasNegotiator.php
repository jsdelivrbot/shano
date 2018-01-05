<?php

namespace Drupal\offcanvas\Theme;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Theme\ThemeNegotiatorInterface;
use Drupal\Core\Routing\RouteMatchInterface;

class OffcanvasNegotiator implements ThemeNegotiatorInterface {

  /**
   * {@inheritdoc}
   */
  public function applies(RouteMatchInterface $route_match) {
    foreach (_offcanvas_get_entities() as $entity_type => $bundles) {
      if ($entity = \Drupal::request()->attributes->get($entity_type, NULL)) {
        if (_offcanvas_validate_entity($entity, $bundles)) {
          return TRUE;
        }
      }
    };

    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function determineActiveTheme(RouteMatchInterface $route_match) {
    // @todo Implement config form and provide theme selector.
    return 'beaufix_offcanvas';
  }
}
