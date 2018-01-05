<?php

/**
 * @files
 * Contains the temporary giftshop item service. *
 *
 * This item is used to collect the data between multiple forms before pushing
 * them to the Giftshop Cart Service.
 *
 * @see \Drupal\giftshop\GiftshopCart
 */

namespace Drupal\giftshop;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Session\SessionManagerInterface;
use Drupal\user\PrivateTempStoreFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class GiftshopCartTempItem
 *
 * @package Drupal\giftshop
 */
class GiftshopCartTempItem implements GiftshopCartTempItemInterface, ContainerInjectionInterface {

  /**
   * The private temp store for the temporary item.
   *
   * @var \Drupal\user\PrivateTempStore $storage
   */
  protected $storage;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('user.private_tempstore'),
      $container->get('session_manager'),
      $container->get('current_user')
    );
  }

  /**
   * @return bool
   */
  public function getUuid() {
    return FALSE;
  }

  /**
   * GiftshopCartTempItem constructor.
   *
   * @param \Drupal\user\PrivateTempStoreFactory $privateTempStore
   * @param \Drupal\Core\Session\SessionManagerInterface $sessionManager
   * @param \Drupal\Core\Session\AccountInterface $currentUser
   */
  public function __construct(PrivateTempStoreFactory $privateTempStore, SessionManagerInterface $sessionManager, AccountInterface $currentUser) {
    $this->storage = $privateTempStore->get('giftshop.cart.temp');

    // Start a manual session for anonymous users.
    if ($currentUser->isAnonymous() && !isset($_SESSION['giftshop'])) {
      $_SESSION['giftshop'] = TRUE;
      $sessionManager->start();
    }
  }

  /**
   * {@inheritdoc}
   */
  public function reset() {

    $this->setNodeId(NULL)
      ->setQuantity(NULL)
      ->setResponseType(NULL)
      ->setResponseData([]);

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getNodeId() {
    return $this->storage->get('entity_id');
  }

  /**
   * {@inheritdoc}
   */
  public function setNodeId($entity_id) {
    $this->storage->set('entity_id', $entity_id);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getQuantity() {
    return $this->storage->get('quantity');
  }

  /**
   * {@inheritdoc}
   */
  public function setQuantity($quantity) {
    $this->storage->set('quantity', $quantity);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setResponseType($type) {
    $this->storage->set('response_type', $type);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getResponseType() {
    return $this->storage->get('response_type');
  }

  /**
   * {@inheritdoc}
   */
  public function setResponseData(array $data) {
    $this->storage->set('response_data', $data);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getResponseData() {
    $data = $this->storage->get('response_data');

    if (!is_array($data)) {
      $data = [$data];
    }

    return $data;
  }

  /**
   * Gets a storable Cart item
   *
   * @return GiftshopCartItemInterface
   */
  public function getStorableItem() {
    $item = new GiftshopCartItem();

    $item->setNodeId($this->getNodeId())
      ->setQuantity($this->getQuantity())
      ->setResponseType($this->getResponseType())
      ->setResponseData($this->getResponseData());

    return $item;
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
