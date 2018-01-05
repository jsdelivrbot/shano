<?php
/**
 * @file
 * Provides Drupal\payment_gateway\Plugin\PaymentGatewayManager.
 */

namespace Drupal\payment_gateway\Plugin;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;

/**
 * Class PaymentGatewayManager
 * @package Drupal\payment_gateway\Plugin
 */
class PaymentGatewayManager extends DefaultPluginManager implements PaymentGatewayManagerInterface
{
  /**
   * Constructs an PaymentGatewayPluginManager object.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations,
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler)
  {
    parent::__construct('Plugin/PaymentGateway', $namespaces, $module_handler, NULL, 'Drupal\payment_gateway\Annotation\PaymentGateway');

    $this->alterInfo('payment_gateway_info');
    $this->setCacheBackend($cache_backend, 'payment_gateway');
  }

  /**
   * {@inheritdoc}
   */
  public function getPaymentGatewayOptions()
  {
    $temp = [];

    foreach ($this->getDefinitions() as $plugin_id => $definition) {
      $temp[$definition['weight']][$plugin_id] = (string)$definition['label'];
    }

    ksort($temp);
    $options = [];
    foreach ($temp as $weight => $value) {
      $key = key($value);
      $options[$key] = $value[$key];
    }

    return $options;
  }

  /**
   * @param array $options
   * @return false|object
   */
  public function getInstance(array $options)
  {
    if (isset($options['id'])) {
      return $this->createInstance($options['id']);
    }
    return parent::getInstance($options);
  }
}
