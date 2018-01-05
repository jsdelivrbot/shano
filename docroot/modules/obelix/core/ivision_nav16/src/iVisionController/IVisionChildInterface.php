<?php
/**
 * User: MoeVoe
 * Date: 30.04.16
 * Time: 16:33
 */

namespace Drupal\ivision_nav16\iVisionController;

/**
 * Interface IVisionChildInterface
 * @package iVisionController
 */
interface IVisionChildInterface {
  /**
   * Get Child Multimedia URL
   * Returns all RMT media of a child.
   * All params are optional filters.
   * @param $childID
   *  project_ID - sequence_number
   *  e.g. '100145-0534'
   * @param $contenttype
   *  e.g.  'APR', 'CUP'
   * @param $mediacode
   *  e.g. 'PIC'
   * @param $approvalStatus
   *  0, 1
   * @param null $derivative
   * @param $size
   *  Number of media returned.
   * @param array $custom_filter
   * @param IVisionLoggerInterface $logger
   * @return mixed Child media from RMT server.
   * Child media from RMT server.
   * Problem: could return double media because of different derivate type spellings.
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
  );

  /**
   * Get one portrait image from the children.
   *
   * @param $ivision_id
   * @param IVisionLoggerInterface $logger
   * @return array Big image blob.
   * Big image blob.
   */
  public static function getOneImage($ivision_id, IVisionLoggerInterface $logger = NULL);

  /**
   * Get al list of children which a reserved for a special ID.
   *
   * @param $reservation_id
   * @param $size
   *  Number of children returned.
   * @param IVisionLoggerInterface $logger
   * @return array List of children.
   * List of children.
   */
  public static function getChildrenByReservationID($reservation_id, $size = NULL, IVisionLoggerInterface $logger = NULL);

  /**
   * Get the basic data from a child (not stepwise)
   *
   * @param $ivision_id
   *  iVision ID
   * @param IVisionLoggerInterface $logger
   * @return array Basic data from the child.
   * Basic data from the child.
   */
  public static function getOneChild($ivision_id, IVisionLoggerInterface $logger = NULL);

  /**
   * Get Stepwise data from child. It's kind of more detailed data.
   *
   * @param $child_sequence
   * @param IVisionLoggerInterface|NULL $logger
   * @return mixed
   */
  public static function getStepWiseChild($child_sequence, IVisionLoggerInterface $logger = NULL);

}
