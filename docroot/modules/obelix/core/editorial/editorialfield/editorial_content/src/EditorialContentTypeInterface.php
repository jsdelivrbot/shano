<?php

/**
 * @file
 * Contains \Drupal\editorial_content\EditorialContentTypeInterface.
 */

namespace Drupal\editorial_content;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface for defining Editorial content type entities.
 */
interface EditorialContentTypeInterface extends ConfigEntityInterface {
  // Add get/set methods for your configuration properties here.

  /**
   * Gets the editorial content types's category.
   *
   * @return string
   *   The editorial content types's category.
   */
  public function getCategory();

  /**
   * Sets the editorial content types's category.
   *
   * @param string $category
   *   The editorial content types's category.
   *
   * @return $this
   */
  public function setCategory($category);

  /**
   * Gets the editorial content types's description.
   *
   * @return string
   *   The editorial content types's description.
   */
  public function getDescription();

  /**
   * Sets the editorial content types's description.
   *
   * @param string $description
   *   The editorial content types's description.
   *
   * @return $this
   */
  public function setDescription($description);
}
