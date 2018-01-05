<?php
/**
 * @file
 * Provides Drupal\payment_gateway\Plugin\PaymentGatewayInterface.
 */

namespace Drupal\payment_gateway\Plugin;

/**
 * Interface PaymentGatewayInterface
 * @package Drupal\payment_gateway\Plugin
 */
Interface PaymentGatewayInterface {

  /**
   * Gets the label of the gateway.
   *
   * @return string
   *   The label of the gateway.
   */
  public function getLabel();

  /**
   * Gets the weight of the gateway.
   *
   * @return number
   *  The weight of the gateway.
   */
  public function getWeight();

  /**
   * Returns the external payment link.
   * Returns FALSE if no external payment is used.
   *
   * @param $args
   * @return mixed
   */
  public function externalPaymentLink($args);
}
