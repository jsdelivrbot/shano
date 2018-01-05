<?php

namespace Drupal\child;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Validation\Plugin\Validation\Constraint\CountConstraint;
use Drupal\country\Entity\Country;
use Drupal\project\Entity\Project;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Child entities.
 *
 * @ingroup child
 */
interface ChildInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {
  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Child IVision ID.
   *
   * @return int
   *   IVision ID of the Child.
   */
  public function getIVisionID();

  /**
   * Sets the Child iVision ID.
   *
   * @param $ivision_id
   * The unique iVision id of the Child.
   * @return ChildInterface The called Child entity.
   * The called Child entity.
   */
  public function setIVisionID($ivision_id);


  /**
   * Gets the Child creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Child.
   */
  public function getCreatedTime();

  /**
   * Sets the Child creation timestamp.
   *
   * @param int $timestamp
   *   The Child creation timestamp.
   *
   * @return \Drupal\child\ChildInterface
   *   The called Child entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Child published status indicator.
   *
   * Unpublished Child are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Child is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Child.
   *
   * @param bool $published
   *   TRUE to set this Child to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\child\ChildInterface
   *   The called Child entity.
   */
  public function setPublished($published);

  /**
   * Gets the Country Entity of the Child.
   *
   * @return Country
   *   Country Entity of the Child.
   */
  public function getCountry();

  /**
   * Gets the Project Entity of the Child.
   *
   * @return Project
   *   Country Entity of the Child.
   */
  public function getProject();

  /**
   * Gets the Child family name.
   *
   * @return string
   *   Family name of the Child.
   */
  public function getFamilyName();

  /**
   * Gets the Child given name.
   *
   * @return string
   *   Given name of the Child.
   */
  public function getGivenName();

  /**
   * Gets the Child gender description.
   *
   * @return string
   *   Gender description of the Child.
   */
  public function getGenderDesc();

  /**
   * Gets the Child birth date.
   *
   * @return string
   *   Birth date of the Child.
   */
  public function getBirthdate();

  /**
   * Gets the Child sequence number.
   * Sequence number shoul be filled with leading 0.
   *
   * @return int
   *  Sequence number of the Child.
   */
  public function getSequenceNumber();

  /**
   * Gets the Child unique sequence number.
   * It is a combination of (project_id)-(sequence_umber)
   *
   * @return string
   *  Unique sequence number of the Child.
   */
  public function getUniqueSequenceNumber();

  /**
   * Sets the published status of a Child.
   *
   * @param int
   *  flag status for the child
   *  0 = found sponsorship
   *  1 = active
   *  2 = blocked
   *
   * @return \Drupal\child\ChildInterface
   *   The called Child entity.
   */
  public function setStatus($status);

  /**
   * Returns the published status of a Child.
   *
   * @return int
   *  flag status for the child
   *  0 = found sponsorship
   *  1 = active
   *  2 = blocked
   */
  public function getStatus();

  /**
   * Sets the Child block timestamp.
   *
   * @param int $timestamp
   *   The Child block timestamp.
   *
   * @return \Drupal\child\ChildInterface
   *   The called Child entity.
   */
  public function setBlockTime($timestamp);

  /**
   * Gets the Child block time.
   *
   * @return int
   *   Block time of the Child.
   */
  public function getBlockTime();

  /**
   * Block a child for the form process.
   * Child will be blocked for 30 minutes.
   *
   * @return \Drupal\child\ChildInterface
   *   The called Child entity.
   */
  public function blockChildInForm();

  /**
   * Block a child for the suggestion e-mail.
   * Child will be blocked for 2 weeks.
   *
   * @return \Drupal\child\ChildInterface
   *   The called Child entity.
   */
  public function blockChildForSuggestion();

  /**
   * Returns the Child's age.
   *
   * @return int
   *   Age of the Child.
   */
  public function getAge();

  /**
   * Sets the Child to founded sponsorship.
   *
   * @return \Drupal\child\ChildInterface
   *  The called Child entity.
   */
  public function setFoundSponsorship();

  /**
   * Get the block reason.
   *
   * @return string
   *  Returns the block reason.
   */
  public function getBlockedFrom();

  /**
   * Sets the block reason.
   *
   * @param $blocked_from
   * @return ChildInterface The called Child entity.
   * The called Child entity.
   */
  public function setBlockedFrom($blocked_from);

  /**
   * Sets the Child Video Url.
   *
   * @param $child_video_url
   * @return ChildInterface The called Child entity.
   * The called Child entity.
   */
  public function setChildVideoUrl($child_video_url);

  /**
   * Get the Child video url.
   *
   * @return string
   *  Returns the Child video url.
   */
  public function getChildVideoUrl();

}
