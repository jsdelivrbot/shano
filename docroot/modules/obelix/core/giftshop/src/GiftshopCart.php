<?php

/**
 * @files
 * Provides the Giftshop Cart Service.
 */

namespace Drupal\giftshop;

use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Session\SessionManagerInterface;
use Drupal\user\PrivateTempStoreFactory;

/**
 * Class GiftshopService.
 *
 * @package Drupal\giftshop
 */
class GiftshopCart implements GiftshopCartInterface {

  /**
   * The private temp store for the cart.
   *
   * @var \Drupal\user\PrivateTempStore $storage
   */
  protected $storage;

  /**
   * GiftshopCart constructor.
   *
   * @param \Drupal\user\PrivateTempStoreFactory $privateTempStoreFactory
   * @param \Drupal\Core\Session\SessionManagerInterface $sessionManager
   * @param \Drupal\Core\Session\AccountInterface $currentUser
   */
  public function __construct(PrivateTempStoreFactory $privateTempStoreFactory, SessionManagerInterface $sessionManager, AccountInterface $currentUser) {
    $this->storage = $privateTempStoreFactory->get('giftshop.cart');

    // Start a manual session for anonymous users.
    if ($currentUser->isAnonymous() && !isset($_SESSION['giftshop'])) {
      $_SESSION['giftshop'] = TRUE;
      $sessionManager->start();
    }
  }

  /**
   * {@inheritdoc}
   */
  public function createItem() {
    return new GiftshopCartItem();
  }

  /**
   * Adds an item to the cart.
   *
   * @param \Drupal\giftshop\GiftshopCartItemInterface $item
   *   The item to add.
   *
   * @return integer
   *   The index of the new item.
   */
  public function addItem(GiftshopCartItemInterface $item) {
    // Append item.
    $cart = $this->getCart();
    $cart[] = $item;
    $this->setCart($cart);

    // Move array pointer to the end and return key.
    end($cart);
    return key($cart);
  }

  /**
   * Gets a specific item from the cart.
   *
   * @param $index
   *   The index of the item to get.
   *
   * @return \Drupal\giftshop\GiftshopCartItemInterface|boolean
   *   The item if present otherwise FALSE.
   */
  public function getItem($index) {
    $cart = $this->getCart();
    return isset($cart[$index]) ? $cart[$index] : FALSE;
  }

  /**
   * Gets all items from the cart.
   *
   * @return array
   *  An array of GiftshopCartItems keyed by their index.
   */
  public function getItems() {
    return $this->getCart();
  }

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
  public function updateItem($index, GiftshopCartItemInterface $item) {
    $cart = $this->getCart();
    if (isset($cart[$index])) {
      $cart[$index] = $item;
      $this->setCart($cart);
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Removes a specific item from the cart.
   *
   * @param $index
   *   The index of the item to remove.
   *
   * @return boolean
   *   TRUE if the iteam was succesfully removed otherwise FALSE.
   */
  public function removeItem($index, $uuid) {
    $cart = $this->storage->get('cart');

    if (isset($cart[$index])) {
      if ($cart[$index]->getUuid() == $uuid) {
        unset($cart[$index]);
        $this->setCart($cart);
      }
      return TRUE;
    }

    return FALSE;
  }

  public function getTotalPrice() {
    $price = 0;

    foreach ($this->getItems() as $item) {
      $price += $item->getTotalPrice();
    }

    return $price;
  }

  /**
   * Gets the cart array from the private temp storage.
   *
   * @return array
   *   The cart array.
   */
  protected function getCart() {
    $cart = $this->storage->get('cart');
    return is_array($cart) ? $cart : [];
  }

  /**
   * Reorders the items in the cart array and stores them to the private temp
   * storage.
   *
   * @param array $cart
   *   The cart array.
   */
  protected function setCart(array $cart) {
    $this->storage->set('cart', array_values($cart));
  }

  /**
   * Removes all items from Cart.
   *
   * @return mixed
   */
  public function emptyCart() {
    $this->setCart([]);
  }
}
