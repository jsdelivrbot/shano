<?php
/**
 * Created by PhpStorm.
 * User: Felix
 * Date: 01.06.16
 * Time: 15:17
 */

namespace Drupal\ivision\iVisionController;


class IVisionGift extends IVisionBase implements IVisionGiftInterface
{

    /**
     * Retrieve Gift
     * Given a Gift ID, this method will return the details for the gift.
     *
     * <domain>/api/gift/<siteID>/<languageCode>/<iVisionID>
     *
     * @param $IVisionID
     *  Required
     * @return mixed
     *  200, 400, 500 – details see general section
     * @throws IVisionException
     */
    public static function getGift($IVisionID)
    {
        $uri = "gift/" . parent::getSiteID() . "/" . parent::getLanguage() . "/" . $IVisionID;
        return parent::apiRequest("GET", $uri);
    }

    /**
     * Retrieve All Gifts
     * This method will return the details for the all gifts.

     * <domain>/api/gift/<siteID>/AllGifts/<languageCode>
     *
     * @return mixed
     *  200, 400, 500 – details see general section
     */
    public static function getAllGifts()
    {
        $uri = "gift/" . parent::getSiteID() . "/AllGifts/" . parent::getLanguage();
        return parent::apiRequest("GET", $uri);
    }

    /**
     * Retrieve Gifts for a Category
     * This method will return gifts within the specified category.

     * <domain>/api/gift/GiftsForCategory/<siteID>/<languageCode>/<giftCategory>
     *
     * @param $GiftCategory
     *  Required
     * @return mixed
     *  200, 400, 500 - See General section for details
     * @throws IVisionException
     */
    public static function getGiftsforCategory($GiftCategory)
    {
        $uri = "gift/" . "/GiftsForCategory/" . parent::getSiteID() . "/" . parent::getLanguage() . "/" . $GiftCategory;
        return parent::apiRequest("GET", $uri);
    }

    /**
     * Retrieve Gifts for a Price Range
     * This method will return gifts that are in the given price range.

     * <domain>/api/gift/GiftsForRange/<siteID>/<languageCode>/<LPrice>/<HPrice>
     *
     * @param $LPrice
     *  Valid decimal
     * @param $HPrice
     *  Greater than lower price
     * @return mixed
     *  200, 400, 500 - See General section for details
     * @throws IVisionException
     */
    public static function getGiftsforPriceRange($LPrice, $HPrice)
    {
        $uri = "gift/" . "/GiftsForRange/" . parent::getSiteID() . "/" . parent::getLanguage() . "/" . $LPrice . "/" . $HPrice;
        return parent::apiRequest("GET", $uri);
    }

    /**
     * Retrieve Gift Categories
     * This method will return the gift categories from iVision.

     * <domain>/api/gift/<siteID>/categories/<languageCode>
     *
     * @return mixed
     *  200, 400, 500 – details see general section
     */
    public static function getGiftCategories()
    {
        $uri = "gift/" . parent::getSiteID() . "/categories/" . parent::getLanguage();
        return parent::apiRequest("GET", $uri);
    }

    /**
     * Related Gifts for Category
     * Given a language code and a category, this method will return a number of gifts in the same category.
     * The gift defined in the Filter Gift parameter will not be part of the return set.
     * Each gift entity will be fully populated, with string values returned in the selected language.
     * If a translation cannot be found the attribute will be returned in the default language configured on the service.
     *
     * If the FilterGiftiVisionID is not set or zero, no filtering will be returned.
     * If the MaxNumberToReturn is not set or zero, a default number of gifts (e.g. 3) will be returned.

     * <domain>/api/gift/RelatedGiftsForCategory/<siteID>/<languageCode>/<categoryName>/<filter ID>/<max>
     *
     * @param $GiftCategory
     *  Required
     * @param $FilterID
     *  Required Can be zero.
     * @param $MaxNumberToReturn
     *  integer
     * @return mixed
     *  200, 400, 500 - See General section for details
     * @throws IVisionException
     */
    public static function getRelatedGiftsforCategory($GiftCategory ,$FilterID, $MaxNumberToReturn)
    {
        $uri = "gift/" . "/RelatedGiftsForCategory/" . parent::getSiteID() . "/" . parent::getLanguage() . "/" . $GiftCategory . "/" . $FilterID . "/" . $MaxNumberToReturn;
        return parent::apiRequest("GET", $uri);
    }
}
