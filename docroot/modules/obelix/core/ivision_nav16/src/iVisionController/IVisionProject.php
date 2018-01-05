<?php
/**
 * User: MoeVoe
 * Date: 30.04.16
 * Time: 16:30
 */

namespace Drupal\ivision_nav16\iVisionController;


class IVisionProject extends IVisionBase implements IVisionProjectInterface {

  /**
   * @inheritdoc
   */
  public static function getSponsorChildrenStatistics($project_id, IVisionLoggerInterface $logger = NULL) {
    $uri = '/Page/RetrieveSponsorChildrenStatistics';
    $params = ['ProjectID' => $project_id];
    return parent::apiRequest('Read', $uri, $params, 'RetrieveSponsorChildrenStatistics', $logger);
  }

  /**
   * @inheritdoc
   */
  public static function getProjectMultiMediaURL(
    $projectID = NULL,
    $contenttype = NULL,
    $mediacode = NULL,
    $approvalStatus = NULL,
    $derivative = NULL,
    $size = NULL,
    IVisionLoggerInterface $logger = NULL) {
    $uri = '/Page/ProjectMultiMediaURL';

    $filter = [];
    if ($projectID) {
      $filter[] = ['Field' => 'Projectcode', 'Criteria' => $projectID];
    }
    if ($contenttype) {
      $filter[] = ['Field' => 'Contenttype', 'Criteria' => $contenttype];
    }
    if ($mediacode) {
      $filter[] = ['Field' => 'Mediacode', 'Criteria' => $mediacode];
    }
    if ($approvalStatus !== NULL) {
      $filter[] = ['Field' => 'ApprovalStatus', 'Criteria' => $approvalStatus];
    }

    if (!$size) {
      $size = 0;
    }

    $params = [
      'filter' => $filter,
      'setSize' => $size
    ];

    $result = parent::apiRequest('ReadMultiple', $uri, $params, 'ProjectMultiMediaURL', $logger);

    if ($derivative !== NULL) {
      $derivative_found = FALSE;
      foreach ($result as $list_key => &$media_list) {
        if (isset($media_list->Project_Multi_Media_URL1->Project_Multi_Media_URL1)) {
          foreach ($media_list->Project_Multi_Media_URL1->Project_Multi_Media_URL1 as $media) {
            if (strtoupper($media->Name) == strtoupper($derivative)) {
              unset($media_list->Project_Multi_Media_URL1->Project_Multi_Media_URL1);
              $media->Name = strtoupper($media->Name);
              $media_list->Project_Multi_Media_URL1->Project_Multi_Media_URL1[0] = $media;
              $derivative_found = TRUE;
              continue;
            }
          }
          if (!$derivative_found) {
            unset($result[$list_key]);
          }
        }
      }
    }

    return $result;
  }
}

