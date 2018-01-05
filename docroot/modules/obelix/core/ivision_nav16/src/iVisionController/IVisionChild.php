<?php
/**
 * User: MoeVoe
 * Date: 30.04.16
 * Time: 16:30
 */

namespace Drupal\ivision_nav16\iVisionController;


class IVisionChild extends IVisionBase implements IVisionChildInterface {


  /**
   * @inheritdoc
   */
  public static function getChildMultiMediaURL(
    $childID = NULL,
    $contenttype = NULL,
    $mediacode = NULL,
    $approvalStatus = NULL,
    $derivative = NULL,
    $size = NULL,
    array $custom_filter = NULL,
    IVisionLoggerInterface &$logger = NULL
  ) {

    $uri = '/Page/ChildMultiMediaURL';

    $filter = [];
    if ($childID) {
      $filter[] = ['Field' => 'ChildID', 'Criteria' => $childID];
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
    if ($custom_filter) {
      foreach ($custom_filter as $single_filter) {
        $filter[] = $single_filter;
      }
    }
    if (!$size) {
      $size = 0;
    }

    $params = [
      'filter' => $filter,
      'setSize' => $size
    ];

    $result = parent::apiRequest('ReadMultiple', $uri, $params, 'ChildMultiMediaURL', $logger);

    if ($result && !is_array($result)) {
      $temp[] = $result;
      $result = $temp;
    }

    if ($derivative !== NULL && $result) {
      $derivative_found = FALSE;
      foreach ($result as $list_key => &$media_list) {
        if (isset($media_list->Child_Multimedia_URL1->Child_Multimedia_URL1)) {
          foreach ($media_list->Child_Multimedia_URL1->Child_Multimedia_URL1 as $media) {
            if (strtoupper($media->Name) == strtoupper($derivative)) {
              unset($media_list->Child_Multimedia_URL1->Child_Multimedia_URL1);
              $media->Name = strtoupper($media->Name);
              $media_list->Child_Multimedia_URL1->Child_Multimedia_URL1[0] = $media;
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


  /**
   * @inheritdoc
   */
  public static function getOneImage($ivision_id, IVisionLoggerInterface $logger = NULL) {
    $uri = '/Page/GetOneImage';
    $params = ['ID' => $ivision_id];
    return parent::apiRequest('Read', $uri, $params, 'GetOneImage', $logger);

  }


  /**
   * @inheritdoc
   */
  public static function getChildrenByReservationID($reservation_id, $size = NULL, IVisionLoggerInterface $logger = NULL) {
    $uri = '/Page/RetrieveChildrenbyReservationID';
    if ($size === NULL) {
      $size = 0;
    }
    $params = [
      'filter' => [
        [
          'Field' => 'ReservationID',
          'Criteria' => $reservation_id,
        ],
        [
          'Field' => 'FOChildStatus',
          'Criteria' => '=AV',
        ],
        [
          'Field' => 'StatusID',
          'Criteria' => 'INH'
        ]
      ],
      'setSize' => $size
    ];

    return parent::apiRequest('ReadMultiple', $uri, $params, 'RetrieveChildrenbyReservationID', $logger);
  }


  /**
   * @inheritdoc
   */
  public static function getOneChild($ivision_id, IVisionLoggerInterface $logger = NULL) {
    $uri = '/Page/RetrieveOneChildfromChildren';
    $params = ['ID' => $ivision_id];
    return parent::apiRequest('Read', $uri, $params, 'RetrieveOneChildfromChildren', $logger);
  }


  /**
   * @inheritdoc
   */
  public static function getStepWiseChild($child_sequence, IVisionLoggerInterface $logger = NULL) {
    $uri = '/Codeunit/RetrieveStepWiseChild';
    $params = [
      'childIDNo' => $child_sequence,
      'retrieveStepWiseChild' => NULL
    ];
    return parent::apiRequest('RetrieveStepWiseChild', $uri, $params, 'retrieveStepWiseChild', $logger)->StepwiseChild;
  }


}
