<?php

namespace Drupal\map;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\map\Entity\GeoObject;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Map entities.
 *
 * @ingroup map
 */
interface MapInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {
  // Add get/set methods for your configuration properties here.
  /**
   * Gets the Map name.
   *
   * @return string
   *   Name of the Map.
   */
  public function getName();

  /**
   * Sets the Map name.
   *
   * @param string $name
   *   The Map name.
   *
   * @return \Drupal\map\MapInterface
   *   The called Map entity.
   */
  public function setName($name);

  /**
   * Gets the Map creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Map.
   */
  public function getCreatedTime();

  /**
   * Sets the Map creation timestamp.
   *
   * @param int $timestamp
   *   The Map creation timestamp.
   *
   * @return \Drupal\map\MapInterface
   *   The called Map entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Map published status indicator.
   *
   * Unpublished Map are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Map is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Map.
   *
   * @param bool $published
   *   TRUE to set this Map to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\map\MapInterface
   *   The called Map entity.
   */
  public function setPublished($published);

  /**
   * Gets the GeoObject list.
   *
   * @return array
   *  Returns list of GeoObject.
   */
  public function getGeoObjects();

}
