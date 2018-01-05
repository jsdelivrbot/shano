<?php
/**
 * User: MoeVoe
 * Date: 30.04.16
 * Time: 16:33
 */

namespace Drupal\ivision_nav16\iVisionController;

/**
 * Interface IVisionSponsorInterface
 * @package iVisionController
 */
interface IVisionSponsorInterface {
  /**
   * Get the data from the sponsor (partner)
   * @param $partner_id
   *  Unique Partner ID
   * @param IVisionLoggerInterface $logger
   * @return array Data from partner
   * Data from partner
   */
  public static function getPartner($partner_id, IVisionLoggerInterface $logger = NULL);

  /**
   * Get the Bank Accounts from Partner
   *
   * @param $partner_id
   *  Unique Partner ID
   * @param $size
   *  Number of entry's.
   * @param IVisionLoggerInterface $logger
   * @return array List of all Bank Accounts
   * List of all Bank Accounts
   */
  public static function getPartnerBankAccounts($partner_id, $size, IVisionLoggerInterface $logger = NULL);

  /**
   * Get Updates from iVision partner db.
   *
   * @param $last_update
   *  Last data update.
   * @param $size
   *  Number of results.
   * @param IVisionLoggerInterface|NULL $logger
   * @return mixed
   */
  public static function getPartnerBaseInfoList($last_update, $size, IVisionLoggerInterface $logger = NULL);

  /**
   * Get Children from sponsor.
   *
   * @param $partner_id
   *  Unique Partner ID
   * @param IVisionLoggerInterface|NULL $logger
   * @return mixed
   */
  public static function getPartnerSponsoredChildren($partner_id, IVisionLoggerInterface $logger = NULL);
}
