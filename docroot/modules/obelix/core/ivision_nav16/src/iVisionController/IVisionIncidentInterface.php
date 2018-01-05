<?php
/**
 * User: MoeVoe
 * Date: 30.04.16
 * Time: 16:33
 */

namespace Drupal\ivision_nav16\iVisionController;

/**
 * Interface IVisionIncidentInterface
 * @package iVisionController
 */
interface IVisionIncidentInterface {
  /**
   * Send data to iVision as an incident.
   *
   * @param array $args
   * Possible fields.
   *  - WebReferenceID
   *  - PartnerID
   *  - FirstName
   *  - MiddleName
   *  - Surname
   *  - DialectName
   *  - JobTitleCode
   *  - CompanyName1
   *  - CompanyName2
   *  - AddressAdditionType
   *  - AddrAddFirstName
   *  - AddrAddSurname
   *  - AddrAddSalutionCode
   *  - AddrAddJobTitleCode
   *  - Street
   *  - HouseNo
   *  - Address2
   *  - Address3
   *  - Department
   *  - PostCode
   *  - City
   *  - CountryCode
   *  - POBox
   *  - POBoxPostCode
   *  - POBoxCity
   *  - TerritoryCode
   *  - POBoxCountryCode
   *  - Email
   *  - PhonePrivate
   *  - MobilePhoneNo
   *  - FaxNo
   *  - LanguageCode
   *  - ChildCountryCode
   *  - ChildGender
   *  - RegionCodePartner
   *  - ActionCode
   *  - PaymentMethod
   *  - BankBranchNo
   *  - BankAccountNo
   *  - SWIFTCode
   *  - IBAN
   *  - CreditCardHolder
   *  - CreditCardExpiryMonth
   *  - CreditCardExpiryYear
   *  - Salutation
   *  - PhoneBusiness
   *  - BankAccountHolder
   *  - PartnerBankAccount
   *  - ProjectID
   *  - ChildSequenceNo
   *  - Gift
   *  - ExternalAddressNo
   *  - AddressType
   *  - CreditCardType
   *  - ExternalReferenceNumber
   *  - ProductCode
   *  - TransmissionDataBuf
   *  - Comment1
   *  - Comment2
   *  - Comment3
   *  - Comment4
   *  - ContinentCode
   *  - InterActionLogEntryCreated
   *  - IncidentType
   *  - RegionCodeChild
   *  - ChildAge
   *  - AmountPerPeriod
   *  - BillingPeriod
   *  - NextSubscriptionDate
   *  - CreditCardSecurityNo
   *  - PledgeType
   *  - ImportDateBuf
   *  - ImportTimeBuf
   *  - DateProcessedBuf
   *  - TimeProcessedBuf
   *  - UserProcessedBuf
   *  - CreditCardNo
   *  - PledgeID
   *  - CatalogueID
   *  - CatalogueQuantity
   *  - PurposeCode
   *  - PaymentID
   *  - Birthdate
   *  - Priority
   *  - TransactionIDBuf
   *  - NoOfBelongingIncidents
   *  - OriginalActionCode
   *  - Affiliates
   *
   * @param IVisionLoggerInterface $logger
   * @return array
   */
  public static function incident(array $args, IVisionLoggerInterface &$logger = NULL);
}
