<?php

/**
 * @file
 * Contains \Drupal\editorial\EditorialContentInterface.
 */

namespace Drupal\editorial;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Editorial content entities.
 *
 * @ingroup editorial_content
 */
interface EditorialContentInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {
  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Editorial content type.
   *
   * @return string
   *   The Editorial content type.
   */
  public function getType();

  /**
   * Gets the Editorial content name.
   *
   * @return string
   *   Name of the Editorial content.
   */
  public function getName();

  /**
   * Sets the Editorial content name.
   *
   * @param string $name
   *   The Editorial content name.
   *
   * @return \Drupal\editorial\EditorialContentInterface
   *   The called Editorial content entity.
   */
  public function setName($name);

  /**
   * Gets the Editorial content creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Editorial content.
   */
  public function getCreatedTime();

  /**
   * Sets the Editorial content creation timestamp.
   *
   * @param int $timestamp
   *   The Editorial content creation timestamp.
   *
   * @return \Drupal\editorial\EditorialContentInterface
   *   The called Editorial content entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Editorial content published status indicator.
   *
   * Unpublished Editorial content are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Editorial content is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Editorial content.
   *
   * @param bool $published
   *   TRUE to set this Editorial content to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\editorial\EditorialContentInterface
   *   The called Editorial content entity.
   */
  public function setPublished($published);

}
