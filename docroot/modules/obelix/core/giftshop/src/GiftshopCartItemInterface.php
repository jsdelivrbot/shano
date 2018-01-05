<?php

/**
 * @file
 * Provides the Interface for the Giftshop Cart Item.
 */

namespace Drupal\giftshop;

/**
 * Interface GiftshopCartItemInterface.
 *
 * @package Drupal\giftshop
 */
interface GiftshopCartItemInterface {

  /**
   * Gets the uuid for this item.
   *
   * @return mixed
   */
  public function getUuid();

  /**
   * Sets the id of the gift entity.
   *
   * @param $id
   *   The entity id.
   * @return \Drupal\giftshop\GiftshopCartItemInterface
   *   The called object.
   */
  public function setNodeId($id);

  /**
   * Gets the gift entity id.
   *
   * @return int
   */
  public function getNodeId();

  /**
   * Sets the quantity for the gift.
   *
   * @param $quantity
   *   The quantity for the gift.
   *
   * @return \Drupal\giftshop\GiftshopCartItemInterface
   *   The called object.
   */
  public function setQuantity($quantity);

  /**
   * Gets the quantity for this gift.
   *
   * @return int
   *   The quantity for this item.
   */
  public function getQuantity();

  /**
   * Sets the response type
   *
   * @param $type
   *   The response type identifier.
   *
   * @return \Drupal\giftshop\GiftshopCartItemInterface
   *   The called object.
   */
  public function setResponseType($type);

  /**
   * Gets the response type.
   *
   * @return string
   *   The response type identifier.
   */
  public function getResponseType();

  /**
   * Sets the extra data for the response.
   *
   * @param array $data
   *   An array of additional data.
   *
   * @return \Drupal\giftshop\GiftshopCartItemInterface
   *   The called object.
   */
  public function setResponseData(array $data);

  /**
   * Gets the extra data of the response.
   *
   * @return array
   *   An array of additional extra data.
   */
  public function getResponseData();

  /**
   * Gets the price of the gift item.
   *
   * @return mixed
   */
  public function getSingleItemPrice();

  /**
   * Gets the price of the gift item multiplied by the quantity.
   *
   * @return mixed
   */
  public function getTotalPrice();
}
