<?php

/**
 * @file
 * Custom search controller to display the amount of search results at top of
 * the page.
 */

namespace Drupal\editorial\Controller;

use Drupal\Core\Render\Element;
use Drupal\editorial\Plugin\Search\CountableSearchInterface;
use Drupal\search\SearchPageInterface;
use Symfony\Component\HttpFoundation\Request;


/**
 * Custom route controller for search.
 */
class SearchController extends \Drupal\search\Controller\SearchController {
  public function view(Request $request, SearchPageInterface $entity) {
    $results = parent::view($request, $entity);

    $results['pager']['#quantity'] = 3;

    $build = [
      '#theme' => 'search_results_page',
      '#attributes' => [
        'class' => ['search-result-wrapper'],
      ],
      '#content' => [],
    ];

    foreach (Element::children($results) as $delta) {
      $build['#content'][$delta] = $results[$delta];
      unset($results[$delta]);
    }

    $build += $results;

    $plugin = $entity->getPlugin();

    if ($plugin instanceof CountableSearchInterface) {
      $build['#content']['search_results_summary'] = [
        '#markup' => $this->t('%count results for %keyword', [
          '%count' => $plugin->getResultCount(),
          '%keyword' => \Drupal::request()->query->get('keys'),
        ]),
      ];
    }

    return $build;
  }
}
