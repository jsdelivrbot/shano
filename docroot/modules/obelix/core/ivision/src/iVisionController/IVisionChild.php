<?php
/**
 * User: MoeVoe
 * Date: 30.04.16
 * Time: 16:30
 */

namespace Drupal\ivision\iVisionController;


class IVisionChild extends IVisionBase implements IVisionChildInterface
{

  /**
   * @inheritdoc
   */
  public static function getChild($iVisionID)
  {
    $uri = "sponsorChild/" . parent::getSiteID() . "/" . parent::getLanguage() . "/Child/" . $iVisionID;
    return parent::apiRequest("GET", $uri);
  }

  /**
   * @inheritdoc
   */
  public static function getChildFromTable($iVisionID)
  {
    $uri = "sponsorChild/" . parent::getSiteID() . "/" . parent::getLanguage() . "/Children/" . $iVisionID;
    return parent::apiRequest("GET", $uri);
  }

  /**
   * @inheritdoc
   */
  public static function getChildImage($iVisionID, $width, $height, $type = 'PictureFolder')
  {
    $uri = "images/" . parent::getSiteID() . "/" . $type . "/" . $iVisionID . "/" . $width . "/" . $height;
    return parent::apiRequest("GET", $uri);
  }

  /**
   * @inheritdoc
   */
  public static function getImagesSince($maxToReturn, $startingID, $height, $width, $date)
  {
    $uri = "Images/" . parent::getSiteID() . "/Child/" . $maxToReturn . "/" . $startingID . "/" . $height . "/" . $width . "/" . $date;
    return parent::apiRequest("GET", $uri);
  }

  /**
   * @inheritdoc
   */
  public static function getChildren($maxToReturn, $startingID)
  {
    $uri = "sponsorChild/" . parent::getSiteID() . "/" . parent::getLanguage() . "/" . $maxToReturn . "/" . $startingID;
    return parent::apiRequest("GET", $uri);
  }

  /**
   * @inheritdoc
   */
  public static function getChildrenByCountryCode($startingID, $maxToReturn, $IVisionId)
  {
    $uri = "sponsorChild/" . parent::getSiteID() . "/" . parent::getLanguage() . "/" . $IVisionId . "/" . $maxToReturn . "/" . $startingID;
    return parent::apiRequest("GET", $uri);
  }


  /**
   * @inheritdoc
   */
  public static function getChildrenByReservationID($reservationId, $maxToReturn, $startingID)
  {
    $uri = "sponsorChild/" . parent::getSiteID() . "/" . parent::getLanguage() . "/" . $reservationId . "/" . $maxToReturn . "/" . $startingID . "/reservationID";
    return parent::apiRequest("GET", $uri);
  }

  /**
   * @inheritdoc
   */
  public static function getChildMediaURL($contentType, $mediaCode, $deriative, $status, $childID)
  {
    $uri = "media/" . parent::getSiteID() . "/ChildMedia/" . $childID . "/" . $contentType . "/" . $mediaCode . "/" . $deriative . "/" . $status;
    return parent::apiRequest("GET", $uri);
  }

  /**
   * @inheritdoc
   */
  public static function getChildMultiMedia($contentType, $mediaCode, $deriative, $status, $childID)
  {
    $uri = "media/" . parent::getSiteID() . "/ChildMultiMedia/" . $childID . "/" . $contentType . "/" . $mediaCode . "/" . $deriative . "/" . $status;
    return parent::apiRequest("GET", $uri);
  }

  /**
   * @inheritdoc
   */
  public static function getChildLanguages()
  {
    $uri = "sponsorChild/" . parent::getSiteID() . "/languages";
    return parent::apiRequest("GET", $uri);
  }

  /**
   * @inheritdoc
   */
  public static function getChildGenders()
  {
    $uri = "sponsorChild/" . parent::getSiteID() . "/" . parent::getLanguage() . "/genders";
    return parent::apiRequest("GET", $uri);
  }

  /**
   * @inheritdoc
   */
  public static function getChildCountries()
  {
    $uri = "sponsorChild/" . parent::getSiteID() . "/" . parent::getLanguage() . "/countries";
    return parent::apiRequest("GET", $uri);
  }

  /**
   * @inheritdoc
   */
  public static function getStepWiseChild($childID)
  {
    $uri = "StepWiseChild/" . parent::getSiteID() . "/" . parent::getLanguage() . "/" . $childID;
    return parent::apiRequest("GET", $uri);
  }

  /**
   * @inheritdoc
   */
  public static function getStepWiseChildSpecial($childID, $loadFamilyMembers, $loadChildParticipations, $loadFamilyParticipations, $loadChildSupports, $loadCorrespondences, $loadVocationalTrainings)
  {
    $uri = "StepWiseChild/" . parent::getSiteID() . "/" . parent::getLanguage() . "/" . $childID . "/" . $loadFamilyMembers . "/" . $loadChildParticipations . "/" . $loadFamilyParticipations . "/" . $loadChildSupports . "/" . $loadCorrespondences . "/" . $loadVocationalTrainings;
    return parent::apiRequest("GET", $uri);
  }

  /**
   * @inheritdoc
   */
  public static function getChildPledgeStatus($requestDate, $onLineType)
  {
    $uri = "pledges/" . parent::getSiteID() . "/childstatus/" . $requestDate;
    return parent::apiRequest("GET", $uri);
  }
}
