<?php

/**
 * @file
 * Provides the interface fot the Giftshop Cart.
 */

namespace Drupal\giftshop;

/**
 * Interface GiftshopServiceInterface.
 *
 * @package Drupal\giftshop
 */
interface GiftshopCartInterface {

  /**
   * Creates an new empty cart item.
   *
   * @return GiftshopCartItem
   */
  public function createItem();

  /**
   * Adds an item to the cart.
   *
   * @param \Drupal\giftshop\GiftshopCartItemInterface $item
   *   The item to add.
   *
   * @return integer
   *   The index of the new item.
   */
  public function addItem(GiftshopCartItemInterface $item);

  /**
   * Gets a specific item from the cart.
   *
   * @param $index
   *   The index of the item to get.
   *
   * @return \Drupal\giftshop\GiftshopCartItemInterface|boolean
   *   The item if present otherwise FALSE.
   */
  public function getItem($index);

  /**
   * Gets all items from the cart.
   *
   * @return array
   *  An array of GiftshopCartItems keyed by their index.
   */
  public function getItems();

  /**
   * Updates a specific item.
   *
   * @param $index
   *   The index of the item to update.
   * @param \Drupal\giftshop\GiftshopCartItemInterface $item
   *   The item to store.
   *
   * @return boolean
   *   TRUE if the item was updated otherwise FALSE.
   */
  public function updateItem($index, GiftshopCartItemInterface $item);

  /**
   * Removes a spcefic item from the cart.
   *
   * @param $index
   *   The index of the item to remove.
   *
   * @param $uuid
   *   The uuid of the item.
   *
   * @return bool TRUE if the iteam was succesfully removed otherwise FALSE.
   * TRUE if the iteam was succesfully removed otherwise FALSE.
   */
  public function removeItem($index, $uuid);

  /**
   * Gets the total price for all items.
   *
   * @return float
   *   The price for all items.
   */
  public function getTotalPrice();

  /**
   * Removes all items from Cart.
   *
   * @return mixed
   */
  public function emptyCart();
}
