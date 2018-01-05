<?php

/**
 * @file
 * Alters Row migrate class to adjust hashing algorithm to hash partial keys & avoid
 * updating nodes every migrate run, so we can increase overall migration performance.
 */

namespace Drupal\wv_simma_connector\Plugin\migrate;

use Drupal\Component\Utility\NestedArray;
use Drupal\migrate\Plugin\MigrateIdMapInterface;

/**
 * Stores a row.
 */
class Row extends \Drupal\migrate\Row {

  /**
   * Recalculates the hash for the row.
   */
  public function rehash() {
    $this->idMap['original_hash'] = $this->idMap['hash'];
    $source = $this->source;

    // Id is temporary, often changed value on remote side, we have no to consider it for hash checks
    // otherwise it will update not updated entity every migrate invocation.
    unset($source['id']);

    // Media assets are fetched by external API & can be ignored too.
    unset($source['image'], $source['video_url']);

    // Unset migrate config from hash too.
    unset($source['track_changes']);

    $this->idMap['hash'] = hash('sha256', serialize($source));
  }

}
