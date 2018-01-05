<?php

/**
 * @file
 * Contains \Drupal\wv_simma_connector\Plugin\migrate\source\Countries.
 */

namespace Drupal\wv_simma_connector\Plugin\migrate\source;

/**
 * Extract countries from MSSql Simma database.
 *
 * @MigrateSource(
 *   id = "countries"
 * )
 */
class Countries extends Projects {
  /**
   * {@inheritdoc}
   */
  public function query() {
    // Select published posts.
    $query = $this->select('projects', 'p')
      ->fields('p', array_keys($this->projectFields()))
      ->groupBy('country_code');

    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function entityTypeId() {
    return 'children';
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return array(
      'country_code' => array(
        'type' => 'string',
        'alias' => 'p',
      ),
    );
  }
}
