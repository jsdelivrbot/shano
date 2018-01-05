<?php
/**
 * User: MoeVoe
 * Date: 30.04.16
 * Time: 16:33
 */

namespace Drupal\ivision_nav16\iVisionController;

/**
 * Interface IVisionProjectInterface
 * @package iVisionController
 */
interface IVisionProjectInterface {
  /**
   * Get information about the children in a project.
   *
   * @param $project_id
   *  Unique Project ID
   * @param IVisionLoggerInterface $logger
   * @return array Information about the children in the project.
   * Information about the children in the project.
   */
  public static function getSponsorChildrenStatistics($project_id, IVisionLoggerInterface $logger = NULL);


  /**
   * Get project Media.
   * @param null $projectID
   *  Unique Project ID
   * @param $contenttype
   *  e.g.  'APR', 'CUP'
   * @param $mediacode
   *  e.g. 'PIC'
   * @param $approvalStatus
   *  0, 1
   * @param null $derivative
   * @param null $size
   * @param $size
   *  Number of media returned.* * @param IVisionLoggerInterface $logger
   * @return
   */
  public static function getProjectMultiMediaURL(
    $projectID = NULL,
    $contenttype = NULL,
    $mediacode = NULL,
    $approvalStatus = NULL,
    $derivative = NULL,
    $size = NULL,
    IVisionLoggerInterface $logger = NULL);
}
