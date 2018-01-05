<?php
/**
 * @file
 * Provides Drupal\payment_gateway\Plugin\PaymentGatewayBase.
 */

namespace Drupal\payment_gateway\Plugin;

use Drupal\Component\Plugin\PluginBase;

/**
 * Class PaymentGatewayBase
 * @package Drupal\payment_gateway\Plugin
 */
abstract class PaymentGatewayBase extends PluginBase implements PaymentGatewayInterface {

  /**
   * {@inheritdoc}
   */
  public function getLabel() {
    return $this->pluginDefinition['label'];
  }

  /**
   * {@inheritdoc}
   */
  public function getWeight() {
    return $this->pluginDefinition['weight'];
  }

  /**
   * {@inheritdoc}
   */
  public function externalPaymentLink($args){
    return FALSE;
  }

}
