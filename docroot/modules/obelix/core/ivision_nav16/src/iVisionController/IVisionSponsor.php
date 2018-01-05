<?php
/**
 * User: MoeVoe
 * Date: 30.04.16
 * Time: 16:30
 */

namespace Drupal\ivision_nav16\iVisionController;


class IVisionSponsor extends IVisionBase implements IVisionSponsorInterface {

  /**
   * @inheritdoc
   */
  public static function getPartner($partner_id, IVisionLoggerInterface $logger = NULL) {
    $uri = '/Page/RetrievePartnerbyPartnerID';
    $params = ['No' => $partner_id];
    return parent::apiRequest('Read', $uri, $params, 'RetrievePartnerbyPartnerID', $logger);
  }

  /**
   * @inheritdoc
   */
  public static function getPartnerBankAccounts($partner_id, $size, IVisionLoggerInterface $logger = NULL) {
    $uri = '/Page/RetrieveBankAccounts';
    if ($size === NULL) {
      $size = 0;
    }

    $params = [
      'filter' => [
        'Field' => 'Donor_No',
        'Criteria' => $partner_id,
      ],
      'setSize' => $size,
    ];
    $result = parent::apiRequest('ReadMultiple', $uri, $params, 'RetrieveBankAccounts', $logger);
    if (is_array($result)) {
      return $result;
    }
    else {
      return [$result];
    }
  }

  /**
   * @inheritdoc
   */
  public static function getPartnerBaseInfoList($last_update, $size, IVisionLoggerInterface $logger = NULL) {
    $uri = '/Page/RetrievePartnerBaseInfoList';
    if ($size === NULL) {
      $size = 0;
    }
    $params = [
      'filter' => [
        'Field' => 'Last_Date_Update',
        'Criteria' => $last_update,
      ],
      'setSize' => $size,
    ];
    return parent::apiRequest('ReadMultiple', $uri, $params, 'RetrievePartnerBaseInfoList', $logger);
  }

  /**
   * @inheritdoc
   */
  public static function getPartnerSponsoredChildren($partner_id, IVisionLoggerInterface $logger = NULL) {
    $uri = '/Codeunit/RetrieveMySponsoredChildren';

    $params = [
      'contactNo' => $partner_id,
      'retrieve_MySponsoredChildren' => NULL
    ];

    $result = parent::apiRequest('RetrieveMySponsoredChildren', $uri, $params, 'retrieve_MySponsoredChildren', $logger)->Children;


    if (!is_array($result)) {
      $result = [$result];
    }

    return $result;


  }
}
