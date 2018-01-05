<?php

/**
 * @files
 * Provides the Giftshop Cart Item.
 */

namespace Drupal\giftshop;

use Drupal\node\Entity\Node;

/**
 * Class GiftshopCartItem
 *
 * @package Drupal\giftshop
 */
class GiftshopCartItem implements GiftshopCartItemInterface {

  /**
   * The uuid of this item.
   *
   * @var string
   */
  protected $uuid;

  /**
   * The node id of the gift.
   *
   * @var int
   */
  protected $node_id;

  /**
   * The amount of
   *
   * @var int
   */
  protected $quantity;

  /**
   * The type of response.
   *
   * @var string
   */
  protected $response_type;

  /**
   * An array of extra data.
   *
   * @var array
   */
  protected $response_data;

  /**
   * GiftshopCartItem constructor.
   */
  public function __construct() {
    // @todo use dependency injection instead of drupals service container.
    $this->uuid = \Drupal::service('uuid')->generate();
  }

  /**
   * Gets the uuid of this item.
   *
   * @return string
   */
  public function getUuid() {
    return $this->uuid;
  }

  /**
   * {@inheritdoc}
   */
  public function getNodeId() {
    return $this->node_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setNodeId($node_id) {
    $this->node_id = $node_id;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getQuantity() {
    return $this->quantity;
  }

  /**
   * {@inheritdoc}
   */
  public function setQuantity($quantity) {
    $this->quantity = $quantity;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getResponseType() {
    return $this->response_type;
  }

  /**
   * {@inheritdoc}
   */
  public function setResponseType($type) {
    $this->response_type = $type;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setResponseData(array $data) {
    $this->response_data = $data;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getResponseData() {
    return $this->response_data;
  }

  /**
   * {@inheritdoc}
   */
  public function getSingleItemPrice() {
    $price = 0;

    if ($nid = $this->getNodeId()) {
      $node = Node::load($nid);
      $price = $node->field_gift_price[0]->value;
    }

    return $price;
  }

  /**
   * {@inheritdoc}
   */
  public function getTotalPrice() {
    return $this->getSingleItemPrice() * $this->getQuantity();
  }
}
