<?php
/**
 * User: MoeVoe
 * Date: 04.06.16
 * Time: 22:00
 */

namespace Drupal\ivision;

use Drupal\project\Entity\Project;
use Drupal\datasourcehandler\DatasourceProjectInterface;
use Drupal\ivision\iVisionController\IVisionProject as IVisionControllerProject;

/**
 * Class IVision
 * @package Drupal\ivision
 */
class IVisionProject implements DatasourceProjectInterface
{

  /**
   * @var Project
   */
  private static $project_entity_type;

  /**
   * IVisionProject constructor.
   * Set the child entityTypeManager.
   */
  public function __construct()
  {
    self::$project_entity_type = \Drupal::entityTypeManager()->getStorage('project');
  }

  /**
   * @inheritdoc
   */
  public static function updateProjectData(Project &$project)
  {
    $project_data = IVisionControllerProject::getProjectSponsorChildrenStatistics($project->get('project_id')->value);

    foreach ($project_data as $key => $value) {
      $project->set('field_' . strtolower($key), $value);
    }
    $project->save();
  }
}
