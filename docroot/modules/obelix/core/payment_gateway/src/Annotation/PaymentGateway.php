<?php

/**
 * @file
 * Provides Drupal\payment_gateway\Annotation\PaymentGateway
 */

namespace Drupal\payment_gateway\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a Payment Gateway annotation object.
 *
 * Plugin namespace: Plugin\PaymentGateway
 *
 * @Annotation
 */
class PaymentGateway extends Plugin {

  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The human-readable name.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $label;

  /**
   * The weight of the element.
   *
   * @var number
   */
  public $weight;

}
