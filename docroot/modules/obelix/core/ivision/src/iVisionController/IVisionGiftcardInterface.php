<?php
/**
 * User: MoeVoe
 * Date: 30.04.16
 * Time: 16:33
 */

namespace Drupal\ivision\iVisionController;

/**
 * Interface IVisionGiftcardInterface
 * @package iVisionController
 */
interface IVisionGiftcardInterface
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
    public static function getGiftCardsByGiftCatalogueID($giftCatalogueID);

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
    public static function getGiftCardsByGiftCardID($giftCardID);

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
    public static function getGiftCardsImagesByGiftCardID($giftCardID, $height, $width);
}
