<?php

namespace Drupal\affiliate;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Affiliate entities.
 *
 * @ingroup affiliate
 */
interface AffiliateItemInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {
  // Add get/set methods for your configuration properties here.
  /**
   * Gets the Affiliate type.
   *
   * @return string
   *   The Affiliate type.
   */
  public function getType();

  /**
   * Gets the Affiliate name.
   *
   * @return string
   *   Name of the Affiliate.
   */
  public function getName();

  /**
   * Sets the Affiliate name.
   *
   * @param string $name
   *   The Affiliate name.
   *
   * @return \Drupal\affiliate\AffiliateItemInterface
   *   The called Affiliate entity.
   */
  public function setName($name);

  /**
   * Gets the Affiliate creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Affiliate.
   */
  public function getCreatedTime();

  /**
   * Sets the Affiliate creation timestamp.
   *
   * @param int $timestamp
   *   The Affiliate creation timestamp.
   *
   * @return \Drupal\affiliate\AffiliateItemInterface
   *   The called Affiliate entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Affiliate published status indicator.
   *
   * Unpublished Affiliate are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Affiliate is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Affiliate.
   *
   * @param bool $published
   *   TRUE to set this Affiliate to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\affiliate\AffiliateItemInterface
   *   The called Affiliate entity.
   */
  public function setPublished($published);

  /**
   * Gets the motivation code of a Affiliate.
   *
   * @return string
   *   The motivation code.
   */
  public function getMotivationCode();
}
