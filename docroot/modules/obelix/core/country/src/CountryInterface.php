<?php

namespace Drupal\country;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Country entities.
 *
 * @ingroup country
 */
interface CountryInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {
  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Country Code.
   *
   * @return string
   *   Code of the Country.
   */
  public function getCountryCode();

  /**
   * Sets the Country code.
   *
   * @param string $code
   *   The Country code.
   *
   * @return \Drupal\country\CountryInterface
   *   The called Country entity.
   */
  public function setCountryCode($code);

  /**
   * Gets the Country name.
   *
   * @return string
   *   Name of the Country.
   */
  public function getName();

  /**
   * Sets the Country name.
   *
   * @param string $name
   *   The Country name.
   *
   * @return \Drupal\country\CountryInterface
   *   The called Country entity.
   */
  public function setName($name);

  /**
   * Gets the Country creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Country.
   */
  public function getCreatedTime();

  /**
   * Sets the Country creation timestamp.
   *
   * @param int $timestamp
   *   The Country creation timestamp.
   *
   * @return \Drupal\country\CountryInterface
   *   The called Country entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Country published status indicator.
   *
   * Unpublished Country are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Country is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Country.
   *
   * @param bool $published
   *   TRUE to set this Country to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\country\CountryInterface
   *   The called Country entity.
   */
  public function setPublished($published);

  /**
   * Gets the Country's Continent.
   *
   * @return int
   *   Continent of the Country.
   */
  public function getContinent();


  /**
   * Gets the Country's grammatical article.
   *
   * @return string
   *   Grammatical article of the country.
   */
  public function getSpecialCountryArticle();

}
