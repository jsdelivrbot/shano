<?php

/**
 * @file
 * Contains \Drupal\wv_site\Routing\Routes.
 */

namespace Drupal\wv_site\Routing;

use Symfony\Component\Routing\Route;

/**
 * Defines dynamic routes.
 */
class Routes {

  /**
   * {@inheritdoc}
   */
  public function routes() {
    $routes = array();
    // Returns an array of Route objects.
    $routes['wv_site.hidden_children'] = new Route(
      // Path to attach this route to:
      rtrim(\Drupal::config('wv_site.settings.children')->get('hidden_child_endpoint')
        ?: '/even-voorstellen', " \t\n\r\0\x0B/") . '/{child_alias}',
      // Route defaults:
      array(
        '_controller' => '\Drupal\wv_site\Controller\HiddenChildren::content',
        '_title' => 'Hidden Children',
        'child_alias' => '',
      ),
      // Route requirements:
      array(
        '_permission'  => 'access content',
      )
    );

    return $routes;
  }

}
