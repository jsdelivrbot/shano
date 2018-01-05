<?php
/**
 * User: MoeVoe
 * Date: 30.04.16
 * Time: 16:33
 */

namespace Drupal\ivision\iVisionController;

/**
 * Interface IVisionProjectInterface
 * @package iVisionController
 */
interface IVisionProjectInterface
{
    /**
     * PROJECT MEDIA URL
     * This method will return the latest URL for project media content.

     * <domain>/api/Media/<siteID>/ProjectMedia/<projectCode>/<contentType>/<mediaCode>/<derivative>/<status>
     *
     * @param $projectCode
     *  Required. Project Code to return media for.
     * @param $contentType
     *  Required. Defines content type to return.  E.g. CGV
     * @param $mediaCode
     *  Required. Defines media code to return.  E.g. VID
     * @param $deriative
     *  Required. Defines derivative of media code.  E.g. iPad
     * @param $status
     *  Required.  Determines if approved or unapproved content is to be returned.
     *  If status = Unapproved, unapproved content will be returned.
     *  If status is anything else, approved content will be returned.
     * @return mixed 200, 400, 500 – details see general section
     * 200, 400, 500 – details see general section
     * @throws IVisionException
     */
    public static function getProjectMediaURL($projectCode, $contentType, $mediaCode, $deriative, $status);

    /**
     * PROJECT MEDIA URL WITH CAPTION
     * This method will return the latest URL with Caption for project media content.

     * <domain>/api/Media/<siteID>/ProjectMedia/WithCaption/<projectCode>/<contentType>/<mediaCode>/<derivative>/<status>

     * @param $projectCode
     *  Required. Project Code to return media for.
     * @param $contentType
     *  Required. Defines content type to return.  E.g. CGV
     * @param $mediaCode
     *  Required. Defines media code to return.  E.g. VID
     * @param $deriative
     *  Required. Defines derivative of media code.  E.g. iPad
     * @param $status
     *  Required.  Determines if approved or unapproved content is to be returned.
     *  If status = Unapproved, unapproved content will be returned.
     *  If status is anything else, approved content will be returned.
     * @return mixed 200, 400, 500 – details see general section
     * 200, 400, 500 – details see general section
     * @throws IVisionException
     */
    public static function getProjectMediaCaption($projectCode, $contentType, $mediaCode, $deriative, $status);

    /**
     * PROJECT MEDIA LIST
     * This method will return Project Media List for project Code.

     * <domain>/api/Media/<siteID>/ProjectMedia/<projectCode>
     *
     * @param $projectCode
     *  Required. Project Code to return media for.
     * @return mixed
     *  200, 400, 500 – details see general section
     * @throws IVisionException
     */
    public static function getProjectMedia($projectCode);

    /**
     * PROJECT MEDIA LIST ALL
     *
     * <domain>/api/Media/<siteID>/ProjectMedia/All/<projectCode>/<contentType>/<mediaCode>/<derivative>/<status>
     *
     * @param $projectCode
     *  Required. Project Code to return media for.
     * @param $contentType
     *  Required. Defines content type to return.  E.g. CGV
     * @param $mediaCode
     *  Required. Defines media code to return.  E.g. VID
     * @param $deriative
     *  Required. Defines derivative of media code.  E.g. iPad
     * @param $status
     *  Required.  Determines if approved or unapproved content is to be returned.
     *  If status = Unapproved, unapproved content will be returned.
     *  If status is anything else, approved content will be returned.
     * @return mixed 200, 400, 500 – details see general section
     * 200, 400, 500 – details see general section
     * @throws IVisionException
     */
    public static function getAllProjectMedia($projectCode, $contentType, $mediaCode, $deriative, $status);

    /**
     * Retrieve Sponsor Children Statistics
     * This method will return number of children in of a given Project ID.
     *
     * <domain>/api/sponsorChild/<siteID>/Statistics/<projectID>
     *
     * @param $projectID
     *  Required
     * @return mixed
     *  200, 400, 500 – details see general section
     * @throws IVisionException
     */
    public static function getProjectSponsorChildrenStatistics($projectID);
}
