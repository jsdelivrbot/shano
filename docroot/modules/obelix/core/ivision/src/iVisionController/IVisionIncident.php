<?php
/**
 * Created by PhpStorm.
 * User: Felix
 * Date: 06.06.16
 * Time: 12:10
 */

namespace Drupal\ivision\iVisionController;


class IVisionIncident extends IVisionBase implements IVisionIncidentInterface
{

    /**
     * “Comfort Solution” iVision
     * SUBMITTING AN INCIDENT
     * For the “Comfort Solution” iVision, all requests are placed into the IncidentBuffer table.
     * This is the only method available to get requests into iVision for the comfort solution.
     * <domain>/api/Incident/<siteID>/NewComfortIncident
     *
     * @param array $args
     * @return mixed 201, 400, 500 – details see general section
     * 201, 400, 500 – details see general section
     */
    public static function setComfortSolutionIncident(array $args)
    {
        $uri = "Incident/" . parent::getSiteID() . "/NewComfortIncident";
        return parent::apiRequest("POST", $uri, $args);
    }

    /**
     * Non-“Comfort Solution” iVision
     * There are several ways to submit requests to iVision, when iVision is not the “Comfort” solution.

     * <domain>/api/donation/<siteID>/<onlineType>/giveDonation
     *
     * @param $onlineType
     *  Required, integer
     * @return mixed
     *  201, 400, 500 – details see general section
     *
     * @throws IVisionException
     */
    public static function setNonComfortSolutionIntrayTables($onlineType)
    {
        $uri = "donation/" . parent::getSiteID() . "/" . $onlineType . "/giveDonation";
        return parent::apiRequest("POST", $uri);
    }

    /**
     * SUBMITTING INCIDENT
     * This method will submit an incident into the GeneralIncident table.
     * This is an all-purpose general method. The value of the Incident
     * Type must exist in theDocument Type of uDMSDocumentType table in
     * the iVision database otherwise validation failure exception will throw.

     * <domain>/api/Incident/<siteID>/NewIncident
     *
     * @return mixed
     *  201, 400, 500 – details see general section
     */
    public static function setNonComfortSolutionIncident()
    {
        $uri = "Incident/" . parent::getSiteID() . "/NewIncident";
        return parent::apiRequest("POST", $uri);
    }

    /**
     * SUBMITTING AN ENQUIRY
     * Save enquiry to InTrayGeneralEnquiry table.
     •	OnlineType = 0 is company, 1 is person

     *
     * <domain>/api/Enquiry/<siteID>/<onlineType>/NewEnquiry
     *
     * @return mixed
     */
    public static function setEnquiry($onlineType)
    {
        $uri = "Enquiry/" . parent::getSiteID() . "/" . $onlineType .   "/NewEnquiry";
        return parent::apiRequest("POST", $uri);
    }
}
