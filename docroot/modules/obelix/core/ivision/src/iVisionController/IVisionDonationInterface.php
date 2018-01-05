<?php
/**
 * User: MoeVoe
 * Date: 30.04.16
 * Time: 16:33
 */

namespace Drupal\ivision\iVisionController;

/**
 * Interface IVisionDonationInterface
 * @package iVisionController
 */
interface IVisionDonationInterface
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
    public static function getDonationPrograms();
}
