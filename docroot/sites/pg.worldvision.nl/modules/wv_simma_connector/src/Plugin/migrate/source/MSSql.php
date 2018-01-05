<?php

/**
 * @file
 * Contains \Drupal\wv_simma_connector\Plugin\migrate\source\MSSql.
 */

namespace Drupal\wv_simma_connector\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\Core\Database\ConnectionNotDefinedException;
use Drupal\Core\Database\Database;
use Drupal\migrate\Exception\RequirementsException;

/**
 * Uses Microsoft SQL database.
 *
 * @MigrateSource(
 *   id = "mssql"
 * )
 */
abstract class MSSql extends SqlBase {
  /**
   * Gets a connection to the referenced database.
   *
   * This method will add the database connection if necessary.
   *
   * @param array $database_info
   *   Configuration for the source database connection. The keys are:
   *    'key' - The database connection key.
   *    'target' - The database connection target.
   *    'database' - Database configuration array as accepted by
   *      Database::addConnectionInfo.
   *
   * @return \Drupal\Core\Database\Connection
   *   The connection to use for this plugin's queries.
   *
   * @throws \Drupal\migrate\Exception\RequirementsException
   *   Thrown if no source database connection is configured.
   */
  protected function setUpDatabase(array $database_info) {
    if (isset($database_info['key'])) {
      $key = $database_info['key'];
    }
    else {
      // If there is no explicit database configuration at all, fall back to a
      // connection named 'migrate'.
      $key = 'migrate';
    }
    if (isset($database_info['target'])) {
      $target = $database_info['target'];
    }
    else {
      $target = 'default';
    }
    if (isset($database_info['database'])) {
      Database::addConnectionInfo($key, $target, $database_info['database']);
    }
    try {
      $connection = Database::getConnection($target, $key);
    }
    catch (ConnectionNotDefinedException $e) {
      // If we fell back to the magic 'migrate' connection and it doesn't exist,
      // treat the lack of the connection as a RequirementsException.
      if ($key == 'migrate') {
        throw new RequirementsException("No database connection configured for source plugin " . $this->pluginId, [], 0, $e);
      }
      else {
        throw $e;
      }
    }
    return $connection;
  }
}
