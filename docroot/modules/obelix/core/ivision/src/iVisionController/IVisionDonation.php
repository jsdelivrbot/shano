<?php
/**
 * Created by PhpStorm.
 * User: Felix
 * Date: 01.06.16
 * Time: 14:42
 */

namespace Drupal\ivision\iVisionController;


/**
 * Class IVisionDonation
 * @package iVisionController
 */
class IVisionDonation extends IVisionBase implements IVisionDonationInterface
{

    /**
     * Retrieve Donation Programs
     * This method will retrieve the donation programs.
     *
     * <domain>/api/donation/<siteID>/<languageCode>
     *
     * @return mixed
     *  200, 400, 500 – details see general section
     */
    public static function getDonationPrograms()
    {
        $uri = "donation/" . parent::getSiteID() . "/" . parent::getLanguage();
        return parent::apiRequest("GET", $uri);
    }
}
