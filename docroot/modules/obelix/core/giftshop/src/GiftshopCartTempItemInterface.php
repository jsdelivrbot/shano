<?php

/**
 * @file
 * Contains the interface for the temporary giftshop item service.
 */

namespace Drupal\giftshop;

/**
 * Interface GiftshopCartTempItemInterface.
 *
 * @package Drupal\giftshop
 */
interface GiftshopCartTempItemInterface extends GiftshopCartItemInterface {

  /**
   * Resets the temporary item.
   *
   * @return \Drupal\giftshop\GiftshopCartTempItemInterface
   *   The called object.
   */
  public function reset();

  /**
   * Gets a storable item
   *
   * @return GiftshopCartItemInterface
   */
  public function getStorableItem();
}
