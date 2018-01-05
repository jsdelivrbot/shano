<?php

/**
 * @file
 * Contains the 'SEPA' payment_gateway.
 */

namespace Drupal\fs_payment_payone\Plugin\PaymentGateway;

use Drupal\Core\Annotation\Translation;
use Drupal\fs_payment_payone\Plugin\PayoneBase;
use Drupal\payment_gateway\Annotation\PaymentGateway;

/**
 * Provides the definition for the payone paypal payment_gateway.
 *
 * @PaymentGateway(
 *   id = "2",
 *   label = @Translation("Paypal"),
 *   weight = 1
 * )
 */
class PayonePaypal extends PayoneBase {

}

