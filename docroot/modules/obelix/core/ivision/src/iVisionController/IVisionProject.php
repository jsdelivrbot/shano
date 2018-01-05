<?php
/**
 * Created by PhpStorm.
 * User: Felix
 * Date: 06.06.16
 * Time: 11:37
 */

namespace Drupal\ivision\iVisionController;


class IVisionProject extends IVisionBase implements IVisionProjectInterface
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
    public static function getProjectMediaURL($projectCode, $contentType, $mediaCode, $deriative, $status)
    {
        $uri = "Media/" . parent::getSiteID() . "/ProjectMedia/" . $projectCode . "/" . $contentType . "/" . $mediaCode . "/" . $deriative . "/" . $status;
        return parent::apiRequest("GET", $uri);
    }

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
    public static function getProjectMediaCaption($projectCode, $contentType, $mediaCode, $deriative, $status)
    {
        $uri = "Media/" . parent::getSiteID() . "/ProjectMedia/WithCaption/" . $projectCode . "/" . $contentType . "/" . $mediaCode . "/" . $deriative . "/" . $status;
        return parent::apiRequest("GET", $uri);
    }

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
    public static function getProjectMedia($projectCode)
    {
        $uri = "Media/" . parent::getSiteID() . "/ProjectMedia/" . $projectCode;
        return parent::apiRequest("GET", $uri);
    }

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
    public static function getAllProjectMedia($projectCode, $contentType, $mediaCode, $deriative, $status)
    {
        $uri = "Media/" . parent::getSiteID() . "/ProjectMedia/All/" . $projectCode . "/" . $contentType . "/" . $mediaCode . "/" . $deriative . "/" . $status;
        return parent::apiRequest("GET", $uri);
    }

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
    public static function getProjectSponsorChildrenStatistics($projectID)
    {
        $uri = "sponsorChild/" . parent::getSiteID() . "/Statistics/" . $projectID;
        return parent::apiRequest("GET", $uri);
    }
}
