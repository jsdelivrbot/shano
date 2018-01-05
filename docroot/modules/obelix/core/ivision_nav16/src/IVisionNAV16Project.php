<?php
/**
 * User: MoeVoe
 * Date: 04.06.16
 * Time: 22:00
 */

namespace Drupal\ivision_nav16;

use Drupal\ivision_nav16\iVisionController\IVisionException;
use Drupal\ivision_nav16\iVisionController\IVisionLogger;
use Drupal\ivision_nav16\iVisionController\IVisionProject;
use Drupal\project\Entity\Project;
use Drupal\datasourcehandler\DatasourceProjectInterface;

/**
 * Class IVision NAV16
 * @package Drupal\ivision_nav16
 */
class IVisionNAV16Project implements DatasourceProjectInterface {

  /**
   * @var Project
   */
  private static $project_entity_type;

  /**
   * IVisionProject constructor.
   * Set the child entityTypeManager.
   */
  public function __construct() {
    self::$project_entity_type = \Drupal::entityTypeManager()
      ->getStorage('project');
  }

  /**
   * @inheritdoc
   */
  public static function updateProjectData(Project &$project) {

    $ignore_keys = ['Key', 'ProjectID','Sponsored', 'Available', 'Dropped'];

    $logger = new IVisionLogger();
    try {
      $project_data = (array)IVisionProject::getSponsorChildrenStatistics($project->get('project_id')->value, $logger);
    } catch (IVisionException $e) {
      \Drupal::logger('ivision_nav16')->error($logger->renderLogs());
      return NULL;
    }
    \Drupal::logger('ivision_nav16')->notice($logger->renderLogs());

    $project_data['numberofsponsored'] = $project_data['Sponsored'];
    $project_data['numberofunsponsored'] = $project_data['Available'];
    $project_data['numberofdropped'] = $project_data['Dropped'];

    foreach ($project_data as $key => $value) {
      if (!in_array($key, $ignore_keys)) {
        $project->set('field_' . strtolower($key), $value);
      }
    }
    $project->save();
  }
}
