<?php
/**
 * User: MoeVoe
 * Date: 04.06.16
 * Time: 22:00
 */

namespace Drupal\datasourcehandler;

use Drupal\project\Entity\Project;

/**
 * Interface DatasourceProjectInterface
 * @package Drupal\datasourcehandler
 */
interface DatasourceProjectInterface
{

    /** Update the Project information's for one Project
     *
     * @param $project Project
     * @return Project
     */
    public static function updateProjectData(Project &$project);

}
