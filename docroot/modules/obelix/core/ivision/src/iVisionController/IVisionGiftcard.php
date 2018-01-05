<?php
/**
 * Created by PhpStorm.
 * User: Felix
 * Date: 01.06.16
 * Time: 14:56
 */

namespace Drupal\ivision\iVisionController;


class IVisionGiftcard extends IVisionBase implements IVisionGiftcardInterface
{

    /**
     * Retrieve Gift Cards By GiftCatalogueID
     * This method will return the gift cards information from iVision.
     *
     * <domain>/api/<siteID>/GiftCardByGiftID/<giftCatalogueID>/<languageCode>
     *
     * @param $giftCatalogueID
     *  Required
     * @return mixed
     *  200, 400, 500 – details see general section
     * @throws IVisionException
     */
    public static function getGiftCardsByGiftCatalogueID($giftCatalogueID)
    {
        $uri = parent::getSiteID() . "/GiftCardByGiftID/" . $giftCatalogueID . "/" . parent::getLanguage();
        return parent::apiRequest("GET", $uri);
    }

    /**
     * Retrieve Gift Cards By GiftCardID
     * This method will return the gift cards information from iVision.
     * <domain>/api/<siteID>/GiftCardByID/<giftCardID>/<languageCode>
     *
     * @param $giftCardID
     *  Required
     * @return mixed
     *  200, 400, 500 – details see general section
     * @throws IVisionException
     */
    public static function getGiftCardsByGiftCardID($giftCardID)
    {
        $uri = parent::getSiteID() . "/GiftCardByID/" . $giftCardID . "/" . parent::getLanguage();
        return parent::apiRequest("GET", $uri);
    }

    /**
     * Retrieve Gift Card Images By GiftCardID
     * This method will return the gift card images from iVision.
     *
     * <domain>/api/<siteID>/GiftCardImagesByID/<giftCardID>/<languageCode>/{height}/{width}
     * @param $giftCardID
     *  Required
     * @param $height
     *  Required
     * @param $width
     *  Required
     * @return mixed
     *  200, 400, 500 – details see general section
     * @throws IVisionException
     */
    public static function getGiftCardsImagesByGiftCardID($giftCardID, $height, $width)
    {
        $uri = parent::getSiteID() . "/GiftCardImagesByID/" . $giftCardID . "/" . parent::getLanguage() . $height . "/" . $width;
        return parent::apiRequest("GET", $uri);
    }
}
