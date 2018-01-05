<?php
/**
 * User: MoeVoe
 * Date: 30.04.16
 * Time: 16:33
 */

namespace Drupal\ivision\iVisionController;

/**
 * Interface IVisionIncidentInterface
 * @package iVisionController
 */
interface IVisionIncidentInterface
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
    public static function setComfortSolutionIncident(array $args);

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
    public static function setNonComfortSolutionIntrayTables($onlineType);

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
    public static function setNonComfortSolutionIncident();

    /**
     * SUBMITTING AN ENQUIRY
     * Save enquiry to InTrayGeneralEnquiry table.
        •	OnlineType = 0 is company, 1 is person

     *
     * <domain>/api/Enquiry/<siteID>/<onlineType>/NewEnquiry
     *
     * @return mixed
     */
    public static function setEnquiry($onlineType);
}
