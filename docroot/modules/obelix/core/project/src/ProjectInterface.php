<?php

namespace Drupal\project;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\country\Entity\Country;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Project entities.
 *
 * @ingroup project
 */
interface ProjectInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {
  // Add get/set methods for your configuration properties here.
  /**
   * Gets the Project ID.
   *
   * @return number
   *   ID of the Project.
   */
  public function getProjectID();

  /**
   * Sets the Project ID.
   *
   * @param number $id
   *   The Project name.
   *
   * @return \Drupal\project\ProjectInterface
   *   The called Project entity.
   */
  public function setProjectID($id);

  /**
   * Gets the Project name.
   *
   * @return string
   *   Name of the Project.
   */
  public function getName();

  /**
   * Sets the Project name.
   *
   * @param string $name
   *   The Project name.
   *
   * @return \Drupal\project\ProjectInterface
   *   The called Project entity.
   */
  public function setName($name);

  /**
   * Gets the Project creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Project.
   */
  public function getCreatedTime();

  /**
   * Sets the Project creation timestamp.
   *
   * @param int $timestamp
   *   The Project creation timestamp.
   *
   * @return \Drupal\project\ProjectInterface
   *   The called Project entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Project published status indicator.
   *
   * Unpublished Project are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Project is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Project.
   *
   * @param bool $published
   *   TRUE to set this Project to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\project\ProjectInterface
   *   The called Project entity.
   */
  public function setPublished($published);

    /**
     * Gets the Country entity
     *
     * @return Country
     *   Returns the Country entity.
     */
    public function getCountry();
}
