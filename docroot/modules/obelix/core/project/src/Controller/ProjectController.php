<?php

namespace Drupal\project\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;

/**
 * Class CountryController.
 *
 * @package Drupal\child\Controller
 */
class ProjectController extends ControllerBase
{
  /**
   * Returns the project options list as assoc array.
   *
   * @return array
   *  Project assoc array.
   */
  public static function getProjectOptions()
  {
    $db = Database::getConnection();
    $results = $db->select('project')
      ->fields('project', ['id', 'name'])->distinct();
    $options = $results->execute()->fetchAllKeyed(0, 1);

    // @todo please set up a mapping function for all iVision fields

    return $options;
  }

}
