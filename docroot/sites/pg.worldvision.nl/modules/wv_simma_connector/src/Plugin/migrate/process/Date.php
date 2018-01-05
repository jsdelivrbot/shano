<?php

namespace Drupal\wv_simma_connector\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\taxonomy\Entity\Term;
use Drupal\migrate\Row;

/**
 * Checks date format & if it's wrongs attempts to fix it.
 *
 * @MigrateProcessPlugin(
 *   id = "date"
 * )
 */
class Date extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   *
   * Split the 'administer nodes' permission from 'access content overview'.
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if (!$value) {
      return NULL;
    }

    $value = preg_replace('/(\d{2}:\d{2}:\d{2}).\d{3}/', '$1', $value);

    return $value;
  }

}
