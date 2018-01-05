<?php
/**
 * @file
 * Provides Drupal\payment_gateway\Plugin\PaymentGatewayManagerInterface
 */

namespace Drupal\payment_gateway\Plugin;

/**
 * Interface PaymentGatewayManagerInterface
 * @package Drupal\payment_gateway\Plugin
 */
interface PaymentGatewayManagerInterface {

  /**
   * Get all available payment gateways as an options array.
   *
   *
   * @return array
   *   Payment Gateways options, as array.
   */
  public function getPaymentGatewayOptions();
}
