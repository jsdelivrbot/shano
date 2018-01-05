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
 * Provides the definition for the payone credit card payment_gateway.
 *
 * @PaymentGateway(
 *   id = "3",
 *   label = @Translation("Credit card"),
 *   weight = 2
 * )
 */
class PayoneCreditCard extends PayoneBase {

}

