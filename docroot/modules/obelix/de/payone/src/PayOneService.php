<?php

namespace Drupal\payone;


/**
 * Class PayOneService.
 *
 * @package Drupal\payone
 */
class PayOneService implements PayOneServiceInterface
{

  static protected $payone_config = array();

  /**
   * Constructor.
   */
  public function __construct()
  {
    self::$payone_config = \Drupal::configFactory()->get('payone.config');
  }

  /**
   *
   */
  public function buildRedirectLink($args, $pay_method, $form_id, $form_name)
  {
    $config = self::$payone_config->get('payone');

    $request = [
      'portalid' => $config['portal_id'],
      'aid' => $config['aid'],
      'mode' => $config['mode'],
      'request' => 'authorization',
      'encoding' => 'UTF-8',
      'currency' => 'EUR',
      'display_name' => 'no',
      'display_address' => 'no',
      'clearingtype' => ($pay_method == 'paypal') ? 'wlt' : 'cc',
      'id' => [1 => $form_id],
      'de' => [1 => $form_name],
      'pr' => [1 => $args['amount']],
      'no' => [1 => 1],
      'va' => [1 => 0],
    ];

    $request = array_merge($request, $args);

    $payone = new PayOne();
    $payone->setFrontendUrl('https://secure.pay1.de/frontend/');
    $payone->setSecret($config['portal_key']);
    return $payone->generateUrlByArray($request);

  }

}
