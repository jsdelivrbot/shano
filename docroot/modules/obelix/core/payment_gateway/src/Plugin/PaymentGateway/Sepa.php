<?php

/**
 * @file
 * Contains the 'SEPA' payment_gateway.
 */

namespace Drupal\payment_gateway\Plugin\PaymentGateway;

use Drupal\Core\Annotation\Translation;
use Drupal\payment_gateway\Annotation\PaymentGateway;
use Drupal\payment_gateway\Plugin\PaymentGatewayBase;

/**
 * Provides the definition for the sepa payment_gateway.
 *
 * @PaymentGateway(
 *   id = "1",
 *   label = @Translation("SEPA"),
 *   weight = 0
 * )
 */
class Sepa extends PaymentGatewayBase {

}

