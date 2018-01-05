<?php

namespace Drupal\editorial\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Listens to the dynamic route events.
 */
class RouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  public function alterRoutes(RouteCollection $collection) {
    if ($route = $collection->get('search.view_node_search')) {
      $route->setDefault('_controller', 'Drupal\editorial\Controller\SearchController::view');
    }
  }
}
