<?php

namespace Drupal\affiliate;

use Drupal\affiliate\Entity\AffiliateItem;
use Drupal\affiliate\Event\AffiliateSessionEvent;
use Drupal\affiliate\Event\AffiliateSessionEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Affiliate Session Manager.
 */
class AffiliateSessionManager {

  protected $eventDispatcher;

  /**
   * AffiliateSessionManager constructor.
   *
   * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
   */
  public function __construct(EventDispatcherInterface $eventDispatcher) {
    $this->eventDispatcher = $eventDispatcher;

  }

  /**
   * Stores the Affiliate Item for this session.
   *
   * @param \Drupal\affiliate\Entity\AffiliateItem $affiliateItem
   *   An Affiliate Item to store.
   */
  public function storeItem(AffiliateItem $affiliateItem) {
    // Set affiliate id cookie until the browser is closed.
    setcookie('refid', $affiliateItem->id(), 0, '/');

    $event = new AffiliateSessionEvent($affiliateItem);
    $this->eventDispatcher->dispatch(AffiliateSessionEvents::STORED, $event);
  }

  /**
   * Restores the Affiliate Item from session.
   *
   * @return \Drupal\Core\Entity\EntityInterface|null
   *   The Affiliate Item if present otherwise NULL.
   */
  public function restoreItem() {
    $affiliateItem = NULL;

    if (!empty($_COOKIE['refid'])) {
      $affiliateItem = \Drupal::entityTypeManager()
        ->getStorage('affiliate')
        ->load($_COOKIE['refid']);

      if ($affiliateItem && $affiliateItem->access('store_in_session')) {
        $event = new AffiliateSessionEvent($affiliateItem);
        $this->eventDispatcher->dispatch(AffiliateSessionEvents::RESTORED, $event);
      }
    }

    return $affiliateItem;
  }
}
