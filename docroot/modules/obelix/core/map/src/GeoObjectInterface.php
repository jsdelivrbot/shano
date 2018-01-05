<?php

namespace Drupal\map;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\country\Entity\Country;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Geo object entities.
 *
 * @ingroup map
 */
interface GeoObjectInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {
  // Add get/set methods for your configuration properties here.
  /**
   * Gets the Geo object name.
   *
   * @return string
   *   Name of the Geo object.
   */
  public function getName();

  /**
   * Sets the Geo object name.
   *
   * @param string $name
   *   The Geo object name.
   *
   * @return \Drupal\map\GeoObjectInterface
   *   The called Geo object entity.
   */
  public function setName($name);

  /**
   * Gets the Geo object creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Geo object.
   */
  public function getCreatedTime();

  /**
   * Sets the Geo object creation timestamp.
   *
   * @param int $timestamp
   *   The Geo object creation timestamp.
   *
   * @return \Drupal\map\GeoObjectInterface
   *   The called Geo object entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Geo object published status indicator.
   *
   * Unpublished Geo object are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Geo object is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Geo object.
   *
   * @param bool $published
   *   TRUE to set this Geo object to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\map\GeoObjectInterface
   *   The called Geo object entity.
   */
  public function setPublished($published);

  /**
   * Gets the JSON.
   *
   * @return string
   *  Returns JSON.
   */
  public function getJson();

  /**
   * Gets the highlighted status.
   *
   * @return bool
   *  Highlighted status.
   */
  public function getHighlighted();

  /**
   * Gets the Country id.
   *
   * @return mixed
   *  Country id.
   */
  public function getCountryID();

}
