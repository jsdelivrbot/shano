<?php
/**
 * Created by PhpStorm.
 * User: Felix
 * Date: 01.06.16
 * Time: 15:52
 */

namespace Drupal\ivision\iVisionController;


class IVisionSponsor extends IVisionBase implements IVisionSponsorInterface
{

    /**
     * Retrieve Partner by Partner ID
     * This method will return partner information given a partner ID.

     * <domain>/api/user/<siteID>/<partnerID>
     *
     * @param $partnerID
     *  Required
     * @return mixed
     *  200, 400, 500 – details see general section
     * @throws IVisionException
     */
    public static function getPartnerById($partnerID)
    {
        $uri = "user/" . parent::getSiteID() . "/" . $partnerID;
        return parent::apiRequest("GET", $uri);
    }

    /**
     * Retrieve Partner by partner id to include additional fields: HasPendingOrder,
     * HasPendingContactInfo, HasPendingLumpsumPayment, PartnerStatus and AddressAddition
     *
     * <domain>/api/user/<siteID>/<partnerID>/<loadHasPendingOrder>/<loadHasPendingContactInfo>/<loadHasPendingPaymentUpdate>/<loadHasPendingLumpSumPayment>/<loadPartnerStatus>/<loadAddressAddition>
     *
     * @param $partnerID
     * @param $loadHasPendingOrder
     *  Required, Y or y load, otherwise not load
     * @param $loadHasPendingContactInfo
     *  Required, Y or y load, otherwise not load
     * @param $loadHasPendingPaymentUpdate
     *  Required, Y or y load, otherwise not load
     * @param $loadHasPendingLumpSumPayment
     *  Required, Y or y load, otherwise not load
     * @param $loadPartnerStatus
     *  Required, Y or y load, otherwise not load
     * @param $loadAddressAddition
     *  Required, Y or y load, otherwise not load
     * @return mixed
     *  200, 400, 500 – details see general section
     * @throws IVisionException
     */
    public static function getPartnerSpecial($partnerID, $loadHasPendingOrder, $loadHasPendingContactInfo, $loadHasPendingPaymentUpdate, $loadHasPendingLumpSumPayment, $loadPartnerStatus, $loadAddressAddition)
    {
        $uri = "user/" . parent::getSiteID() . "/" . $partnerID . "/" . $loadHasPendingOrder . "/" . $loadHasPendingContactInfo . "/" . $loadHasPendingPaymentUpdate . "/" . $loadHasPendingLumpSumPayment . "/" . $loadPartnerStatus . "/" . $loadAddressAddition;
        return parent::apiRequest("GET", $uri);
    }

    /**
     * Retrieve Partner by Portal Account ID
     * This method will return user information from iVision for given portal account ID.
     * At the time of implementation, the portal account ID in iVision is a GUID.
     * Strings passed that are not a GUID will cause a Bad Request response.

     * <domain>/api/user/<siteID>/PortalAccount/<portalAccountGuid>
     *
     * @param $portalAccountGuid
     *  Required
     * @return mixed
     *  200, 400, 500 – details see general section
     * @throws IVisionException
     */
    public static function getPartnerByPortalAccountID($portalAccountGuid)
    {
        $uri = "user/" . parent::getSiteID() . "/PortalAccount/" . $portalAccountGuid;
        return parent::apiRequest("GET", $uri);
    }

    /**
     * Retrieve Partner Titles
     * This method will return the titles available for partners.

     * <domain>/api/user/<siteID>/Titles
     *
     * @return mixed
     *  200, 400, 500 – details see general section
     */
    public static function getPartnerTitles()
    {
        $uri = "user/" . parent::getSiteID() . "/Titles";
        return parent::apiRequest("GET", $uri);
    }

    /**
     * Retrieve Partner Countries
     * This method will return the countries available for partners.

     * <domain>/api/user/<siteID>/Countries
     *
     * @return mixed
     *  200, 400, 500 – details see general section
     */
    public static function getPartnerCountries()
    {
        $uri = "user/" . parent::getSiteID() . "/Countries";
        return parent::apiRequest("GET", $uri);
    }

    /**
     * Retrieve Partner Salutations
     * This method will return the salutations available for partners.

     * <domain>/api/user/<siteID>/Salutations
     *
     * @return mixed
     *  200, 400, 500 – details see general section
     */
    public static function getPartnerSalutations()
    {
        $uri = "user/" . parent::getSiteID() . "/Salutations";
        return parent::apiRequest("GET", $uri);
    }

    /**
     * Retrieve Partner Base Info List
     * This method will return the Sponsor IDs and Email addresses by CreateDate or LastModified Date.
     *
     * <domain>/api/user/<siteID>/PartnerIDs/<date>/<isNew>/<typeID>
     *
     * @param $date
     *  Required, format yyyy-mm-dd
     * @param $isNew
     *  Required,  1 return list which Createddate >= date, 0 return list which LastModifiedDate >= date
     * @param $typeID
     *  Required, 0 All, 1 Only Partners with an active child sponsorship,
     *  2 Only Partners with an inactive child sponsorship, 3 combination of 2 and 3
     * @return mixed
     *  200, 400, 500 – details see general section
     * @throws IVisionException
     */
    public static function getPartnerBaseInfoList($date, $isNew, $typeID)
    {
        $uri = "user/" . parent::getSiteID() . "/PartnerID's/" . $date . "/" . $isNew . "/" . $typeID;
        return parent::apiRequest("GET", $uri);
    }

    /**
     * Retrieve Partner My Sponsored Children
     * This method will return the partner Sponsored Children List that include the sponsored children
     * in active pledge and unprocessed sponsored children in the Intray ChildSponsorship.
     *
     * <domain>/api/user/<siteID>/ MySponsoredChildren /<partnerID>/<languageCode>/
     *
     * @param $partnerID
     *  Required
     * @return mixed
     *  200, 400, 500 – details see general section
     * @throws IVisionException
     */
    public static function getPartnerMySponsoredChildren($partnerID)
    {
        $uri = "user/" . parent::getSiteID() . "/MySponsoredChildren/" . $partnerID . parent::getLanguage() . "/";
        return parent::apiRequest("GET", $uri);
    }

    /**
     * Retrieve Partner Info by Email, Phone Number, Name, First Name, Surname, VAT_Registration No or Birth Date
     * This method will return partner information given a TypeID.

     * <domain>/api/User/{siteID}/{typeID}/{searchvalue}/<loadHasPendingOrder>/<loadHasPendingContactInfo>/<loadHasPendingPaymentUpdate>/<loadHasPendingLumpSumPayment>/<loadPartnerStatus>/<loadAddressAddition>
     *
     * @param $typeID
     *  Required, 1- By Email, 2- By Phone Number or Mobile Number, 3 – By Name,
     *  4 – By First Name, 5 – By Surname, 6 – By VAT Registration No, 7 – By Birth Date
     * @param $loadHasPendingOrder
     * @param $loadHasPendingContactInfo
     * @param $loadHasPendingPaymentUpdate
     * @param $loadHasPendingLumpSumPayment
     * @param $loadPartnerStatus
     * @param $loadAddressAddition
     * @return mixed
     *  200, 400, 500 – details see general section
     * @throws IVisionException
     */
    public static function getPartnerInformationBySearchTypes($typeID, $loadHasPendingOrder, $loadHasPendingContactInfo, $loadHasPendingPaymentUpdate, $loadHasPendingLumpSumPayment, $loadPartnerStatus, $loadAddressAddition)
    {
        $uri = "user/" . parent::getSiteID() . "/" . $typeID . "/" . $loadHasPendingOrder . "/" . $loadHasPendingContactInfo . "/" . $loadHasPendingPaymentUpdate . "/" . $loadHasPendingLumpSumPayment . "/" . $loadPartnerStatus . "/" . $loadAddressAddition;
        return parent::apiRequest("GET", $uri);
    }

    /**
     * <domain>/api/User/{siteID}/{typeID}/{searchvalue}/<loadHasPendingOrder>/<loadHasPendingContactInfo>/<loadHasPendingPaymentUpdate>/<loadHasPendingLumpSumPayment>/<loadPartnerStatus>/<loadAddressAddition>
     *
     * @param $typeID
     *  Required, 1- By Email, 2- By Phone Number or Mobile Number, 3 – By Name,
     *  4 – By First Name, 5 – By Surname, 6 – By VAT Registration No, 7 – By Birth Date
     * @param $loadHasPendingOrder
     *  Required, Y or y load, otherwise not load
     * @param $loadHasPendingContactInfo
     *  Required, Y or y load, otherwise not load
     * @param $loadHasPendingPaymentUpdate
     *  Required, Y or y load, otherwise not load
     * @param $loadHasPendingLumpSumPayment
     *  Required, Y or y load, otherwise not load
     * @param $loadPartnerStatus
     *  Required, Y or y load, otherwise not load
     * @param $loadAddressAddition
     *  Required, Y or y load, otherwise not load
     * @return mixed
     * @throws IVisionException
     */
    public static function getPartnerInformationBySearchTypesSpecial($typeID, $loadHasPendingOrder, $loadHasPendingContactInfo, $loadHasPendingPaymentUpdate, $loadHasPendingLumpSumPayment, $loadPartnerStatus, $loadAddressAddition)
    {
        $uri = "user/" . parent::getSiteID() . "/" . $typeID . "/" . $loadHasPendingOrder . "/" . $loadHasPendingContactInfo . "/" . $loadHasPendingPaymentUpdate . "/" . $loadHasPendingLumpSumPayment . "/" . $loadPartnerStatus . "/" . $loadAddressAddition;
        return parent::apiRequest("GET", $uri);
    }

    /**
     * @return mixed
     */
    public static function getPartnerProfileInformation()
    {
        // TODO: Implement getPartnerProfileInformation() method.
    }

    /**
     * Retrieve Payment Methods
     * This method will return a specific Partner Payment Methods

     * <domain>/api/user/<siteID>/PaymentMethods/<partnerID>/<languageID>
     *
     * @param $partnerID
     *  Required
     * @return mixed
     *  200, 400, 500 – details see general section
     * @throws IVisionException
     */
    public static function getPaymentMethods($partnerID)
    {
        $uri = "user/" . parent::getSiteID() . "/PaymentMethods/" . $partnerID . parent::getLanguage();
        return parent::apiRequest("GET", $uri);
    }

    /**
     * Retrieve Payment Accounts
     * This method will return a specific Partner Payment account information from customer bank account table
     *
     * <domain>/api/user/<siteID>/PaymentAccounts/<partnerID>
     *
     * @param $partnerID
     *  Required
     * @return mixed
     *  200, 400, 500 – details see general section
     * @throws IVisionException
     */
    public static function getPaymentAccounts($partnerID)
    {
        $uri = "user/" . parent::getSiteID() . "/PaymentAccounts/" . $partnerID;
        return parent::apiRequest("GET", $uri);
    }

    /**
     * Retrieve Ongoing Givings
     * Given a partner ID, this method will return the partner’s ongoing givings.

     * <domain>/api/user/<siteID>/OngoingGivings/<partnerID>
     *
     * @param $partnerID
     *  Required
     * @return mixed
     *  200, 400, 500 – details see general section
     * @throws IVisionException
     */
    public static function getOngoingGivings($partnerID)
    {
        $uri = "user/" . parent::getSiteID() . "/OngoingGivings/" . $partnerID;
        return parent::apiRequest("GET", $uri);
    }

    /**
     * Retrieve Payment History
     * Given a partner ID, this method will return the payment history for the partner from on or after
     * the supplied cut-off date, the format of the cut off date is YYYYMMDD

     * <domain>/api/user/<siteID>/PaymentHistory/<partnerID>/<cutoff date>
     *
     * @param $partnerID
     *  Required
     * @param $CutoffDate
     *  Required
     * @return mixed
     *  200, 400, 500 – details see general section
     * @throws IVisionException
     */
    public static function getPaymentHistory($partnerID, $CutoffDate)
    {
        $uri = "user/" . parent::getSiteID() . "/PaymentHistory/" . $partnerID . "/" . $CutoffDate;
        return parent::apiRequest("GET", $uri);
    }

    /**
     * RETRIEVE INTERACTION INFORMATION
     * This method will return the Interaction information from iVision.

     * <domain>/api/InteractionInfo/<siteID>/<partnerID>/<templateCode>/<subjectCode>/<startingID>/<maxToReturn>/<fromDate>
     *
     * @param $partnerID
     *  Required
     * @param $templateCode
     *  Required
     * @param $subjectCode
     *  Required, can be zero
     * @param $startingID
     *  Required, can be zero, it’s[Entry No_] value of [Interaction Log Entry] table
     * @param $maxToReturn
     *  Required, can be zero
     * @param $fromDate
     *  Optional, format yyyy-mm-dd
     * @return mixed
     *  200, 400, 500 – details see general section
     * @throws IVisionException
     */
    public static function getInteractionInformation($partnerID, $templateCode, $subjectCode, $startingID, $maxToReturn, $fromDate)
    {
        $uri = "InteractionInfo/" . parent::getSiteID() . "/" . $partnerID . "/" . $templateCode . "/" . $subjectCode . "/" . $startingID . "/" . $maxToReturn . "/" .$fromDate;
        return parent::apiRequest("GET", $uri);
    }

    /**
     * Update Donor Info
     * This method will place the donor information into the iVision IntrayNewPartnerUpdate,
     * AdditionalMember, IntrayCommPreferences, IntrayPaymentUpdate,
     * InTrayLumpSum and IntrayCatalogueExtText tables based on the requested information.
     * It is used to create a new donor request or update contact information request.
     * •OnlineType = 0 is company, 1 is person
     * •OnlineBankAccountType: 0 – Blank, 1 – Saving, 2 – Checking and 3 – Deposit
     *
     * <domain>/api/user/<siteID>/<onlineType>/UpdateDonorInfo
     *
     * @param $onlineType
     *  Required, integer
     * @return mixed
     *  201, 400, 500 – details see general section
     * @throws IVisionException
     */
    public static function setDonorInfo($onlineType)
    {
        $uri = "user/" . parent::getSiteID() . "/" . $onlineType . "/UpdateDonorInfo";
        return parent::apiRequest("POST", $uri);
    }
}
