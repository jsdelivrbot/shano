<?php
/**
 * User: MoeVoe
 * Date: 30.04.16
 * Time: 16:33
 */

namespace Drupal\ivision\iVisionController;

/**
 * Interface IVisionChildInterface
 * @package iVisionController
 */
interface IVisionChildInterface
{

    /**
     * Retrieve One Child
     * This method will return a single child, given a Child ID and language code,
     * the iVisionID can be the internal ID from the Children table or standard Child ID,
     * sample Child ID format as UGA-170531-0384
     *
     * <domain>/api/sponsorChild/<siteID>/<languageCode>/Child/<iVisionID>
     *
     * @param $iVisionID
     * Required
     * @return array
     * 200, 400, 500 – details see general section
     * @throws IVisionException
     */
    public static function getChild($iVisionID);

    /**
     * Retrieve One Child from Children Table
     *  This method will return a single child from Children table, given a Child ID and language code,
     *  the iVisionID can be the internal ID from the Children table or standard Child ID,
     *  sample Child ID format as UGA-170531-0384.
     *  This method can be used to replace the above method “Retrieve One Child”
     *
     * <domain>/api/sponsorChild/<siteID>/<languageCode>/Children/<iVisionID>
     *
     * @param $iVisionID
     *  Required
     * @return mixed
     *  200, 400, 500 – details see general section
     * @throws IVisionException
     */
    public static function getChildFromTable($iVisionID);

    /**
     * Get One Image
     * The Child iVisionID can be the internal ID from the children table or the standard child ID,
     * sample standard Child ID format as UGA-170531-0384
     *
     * Child Image
     *  <domain>/api/images/<siteID>/Child/<iVisionID>/<height>/<width>
     * Gift Image
     *  <domain>/api/Images/<siteID>/Gift/<iVisionID>/<height>/<width>
     * Donation Image
     *  <domain>/api/Images/<siteID>/Donation/<iVisionID>/<height>/<width>
     *
     * @param $iVisionID
     *  Required
     * @param int $width
     *  Required if Height is set.
     * @param int $height
     *  Required if Width is set.
     * @param $type
     *  could be PictureFolder or Child.
     *  depend on the country iVision
     * @return mixed
     *  200, 400, 500 – details see general section
     * @throws IVisionException
     */
    public static function getChildImage($iVisionID, $width, $height, $type = 'PictureFolder');

    /**
     * Child Images
     *  <domain>/api/Images/<siteID>/Child/<maxToReturn>/<startingID>/<height>/<width>/<yyyymmdd>
     * Gift Images
     *  <domain>/api/Images/<siteID>/Gift/<maxToReturn>/<startingID>/<height>/<width>/<yyyymmdd>
     * Donation Images
     *  <domain>/api/Images/<siteID>/Donation/<maxToReturn>/<startingID>/<height>/<width>/<yyyymmdd>
     *
     * @param $maxToReturn
     *  Required
     * @param $startingID
     *  Required
     * @param $height
     *  Required.  Can be zero.  Non-zero if width is non-zero.
     * @param $width
     *  Required.  Can be zero.  Non-zero if height is non-zero.
     * @param $date
     *  Required
     * @return mixed
     *  200, 400, 500 – details see general section
     * @throws IVisionException
     */
    public static function getImagesSince($maxToReturn, $startingID, $height, $width, $date);

    /**
     * Retrieve Children
     * This method returns a list of children from iVision based on request parameters.
     *
     * <domain>/api/sponsorChild/<siteID>/<languageCode>/<maxReturn>/<startingID>
     *
     * @param $maxToReturn
     *  Required.
     * @param $startingID
     *  Required. Can be zero.
     * @return mixed
     *  200, 400, 500 – details see general section
     * @throws IVisionException
     * @internal param $dhgfhdfh
     */
    public static function getChildren($maxToReturn, $startingID);

    /**
     * Retrieve Children by Country Code or Country ID
     * This method will retrieve a list of children either by country code or by child ID.
     *
     * <domain>/api/sponsorChild/<siteID>/<languageCode>/<countryCodeOrID>/<maxReturn>/<startingID>
     *
     * @param $IVisionId
     *  Required. Can be either country code or iVisionID.
     * @param $startingID
     *  Required. Can be zero.
     * @param $maxToReturn
     *  Required
     * @return mixed
     *  200, 400, 500 – details see general section
     * @throws IVisionException
     */
    public static function getChildrenByCountryCode($startingID, $maxToReturn, $IVisionId);

    /**
     * Retrieve Children by ReservationID
     * This method will return children either by ReservationID.
     *
     * <domain>/api/sponsorChild/<siteID>/<languageCode>/<reservationID>/{maxToReturn}/{startingID}/reservationID
     *
     * @param $reservationId
     *  Required
     * @param $maxToReturn
     *  Required
     * @param $startingID
     *  Required. Can be zero.
     * @return mixed
     *  200, 400, 500 – details see general section
     * @throws IVisionException
     */
    public static function getChildrenByReservationID($reservationId, $maxToReturn,$startingID);

    /**
     * CHILD MEDIA URL
     * This method will return the latest URL for child media content,
     * the childID format is the ProjectID + “-“ + Child SequenceNo in this method or the standard Child ID,
     * sample standard Child ID format as UGA-170531-0384
     *
     * @param $contentType
     *  Required. Defines content type to return. E.g. CGV
     * @param $mediaCode
     *  Required. Defines media code to return. E.g. VID
     * @param $deriative
     *  Required. Defines derivative of media code. E.g. iPad
     * @param $status
     *  Required. Determines if approved or unapproved content is to be returned.
     *  If status = Unapproved, unapproved content will be returned.
     *  If status is anything else, approved content will be returned.
     * @param $childID
     *  Required.  Child ID to return media for.
     * @return mixed
     *  200, 400, 500 – details see general section
     * @throws IVisionException
     */
    public static function getChildMediaURL($contentType, $mediaCode, $deriative, $status, $childID);

    /**
     * CHILD MULTI MEDIA URL
     * This method will return all URL for child media content,
     * the childID format is the ProjectID + “-“ + Child SequenceNo in this method.
     *
     * @param $contentType
     *  Required. Defines content type to return. E.g. CGV
     * @param $mediaCode
     *  Required. Defines media code to return. E.g. VID
     * @param $deriative
     *  Required. Defines derivative of media code. E.g. iPad
     * @param $status
     *  Required. Determines if approved or unapproved content is to be returned.
     *  If status = Unapproved, unapproved content will be returned.
     *  If status is anything else, approved content will be returned.
     * @param $childID
     *  Required.  Child ID to return media for.
     * @return mixed
     *  200, 400, 500 – details see general section
     * @throws IVisionException
     */
    public static function getChildMultiMedia($contentType, $mediaCode, $deriative, $status, $childID);

    /**
     * Child Languages
     * This method returns a distinct list of languages for which content can be pulled.
     *
     * <domain>/api/sponsorChild/<siteID>/languages
     *
     * @return mixed
     * 200, 400, 500 – details see general section
     */
    public static function getChildLanguages();

    /**
     * Child Genders
     * This method returns a distinct list of genders for children, in alphabetical order by Description.

     * <domain>/api/sponsorChild/<siteID>/<languageCode>/genders
     *
     * @return mixed
     *  200, 400, 500 – details see general section
     */
    public static function getChildGenders();

    /**
     * Child Countries
     * This method returns a distinct list of countries for children, in alphabetical order by Description.
     *
     * <domain>/api/sponsorChild/<siteID>/<languageCode>/countries
     *
     * @return mixed
     *  200, 400, 500 – details see general section
     */
    public static function getChildCountries();

    /**
     * Retrieve Step Wise Child
     * This method will return the stepwise child information, stepwise family member,
     * stepwise child participation, stepwise family participation, stepwise child support,
     * stepwise family support, stepwise correspondence and stepwise vocational training.
     *
     * <domain>/api/StepWiseChild/<siteID>/<languageCode>/<childID>
     *
     * @param $childID
     *  Required, Stepwise child ID or SD Child ID
     * @return mixed
     *  200, 400, 500 – details see general section
     * @throws IVisionException
     */
    public static function getStepWiseChild($childID);

    /**
     * Retrieve Step Wise Child
     * SPECIAL
     *
     * <domain>/api/StepWiseChild/<siteID>/<languageCode>/<childID>/<loadFamilyMembers>/<loadChildParticipations>/<loadFamilyParticipations>/<loadChildSupports>/<loadFamilySupports>/<loadCorrespondences>/<loadVocationalTrainings>
     *
     * @param $childID
     *  Required, Stepwise child ID or SD Child ID
     * @param $loadFamilyMembers
     *  Required, Y or y load, otherwise not load
     * @param $loadChildParticipations
     *  Required, Y or y load, otherwise not load
     * @param $loadFamilyParticipations
     *  Required, Y or y load, otherwise not load
     * @param $loadChildSupports
     *  Required, Y or y load, otherwise not load
     * @param $loadCorrespondences
     *  Required, Y or y load, otherwise not load
     * @param $loadVocationalTrainings
     *  Required, Y or y load, otherwise not load
     * @return mixed
     *  200, 400, 500 – details see general section
     * @throws IVisionException
     */
    public static function getStepWiseChildSpecial($childID, $loadFamilyMembers, $loadChildParticipations, $loadFamilyParticipations, $loadChildSupports, $loadCorrespondences, $loadVocationalTrainings);

    /**
     * RETRIEVE CHILD PLEDGES STATUS
     * This service method will return the partner’s child pledges status based on pledge editing date when
     * the partner pledge status updated, the request date format is YYYY-MM-DD, sample as 2012-07-20
     *
     * <domain>/api/pledges/<siteID>/childstatus/{RequestDate}
     *
     * @param $requestDate
     *  Required, sample 2012-07-20
     * @param $onLineType
     *  Required, integer
     * @return mixed
     *  201, 400, 500 – details see general section
     * @throws IVisionException
     */
    public static function getChildPledgeStatus($requestDate, $onLineType);
}
