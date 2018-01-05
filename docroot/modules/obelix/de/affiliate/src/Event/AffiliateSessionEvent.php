<?php

namespace Drupal\affiliate\Event;

use Drupal\affiliate\Entity\AffiliateItem;
use Symfony\Component\EventDispatcher\Event;

class AffiliateSessionEvent extends Event {
  /**
   * The affiliate item.
   *
   * @var \Drupal\affiliate\Entity\AffiliateItem
   */
  protected $affiliateItem;

  /**
   * AffiliateSessionEvent constructor.
   *
   * @param \Drupal\affiliate\Entity\AffiliateItem $affiliateItem
   */
  public function __construct(AffiliateItem $affiliateItem) {
    $this->affiliateItem = $affiliateItem;
  }

  /**
   * Gets the Affiliate Item.
   *
   * @return \Drupal\affiliate\Entity\AffiliateItem
   */
  public function getAffiliateItem() {
    return $this->affiliateItem;
  }

  /**
   * Gets the bundle type of the Affiliate item.
   *
   * @return mixed|string
   */
  public function getAffiliateItemType() {
    return $this->affiliateItem->bundle();
  }
}
