<?php

namespace Drupal\editorial\Plugin\Search;

/**
 * Interface CountableSearchInterface
 */
Interface CountableSearchInterface {

  /**
   * Gets the amount of search results for the current query.
   *
   * @return int
   *   The amount of results.
   */
  public function getResultCount();
}
