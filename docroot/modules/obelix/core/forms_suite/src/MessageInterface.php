<?php

/**
 * @file
 * Contains \Drupal\forms_suite\MessageInterface.
 */

namespace Drupal\forms_suite;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\forms_suite\Entity\Message;

/**
 * Provides an interface defining a forms message entity.
 */
interface MessageInterface extends ContentEntityInterface {

  /**
   * Returns the form this forms message belongs to.
   *
   * @return \Drupal\forms_suite\FormInterface
   *   The forms form entity.
   */
  public function getForm();
  /**
   * Returns TRUE if this is the personal forms form.
   *
   * @return bool
   *   TRUE if the message bundle is personal.
   */
  public function isPersonal();

  /**
   * Returns the user this message is being sent to.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity of the recipient, NULL if this is not a personal message.
   */
  public function getPersonalRecipient();

  /**
   * get the the transfer ID from the webservice
   *
   * @return integer
   * The transfer ID from the webservice.
   */
  public function getTransferID();

  /**
   * Set the the transfer ID from the webservice
   * @param $transfer_id
   *  The transfer ID from the webservice.
   * @return Message
   */
  public function setTransferID($transfer_id);

  /**
   * Get the motivation code from the form submit.
   *
   * @return integer
   * The motivation code
   */
  public function getMotivationCode();

  /**
   * Set the motivation code from the form submit.
   *
   * @param $motivation_code
   *  The motivation code
   * @return Message
   */
  public function setMotivationCode($motivation_code);

  /**
   * Get the motivation code history.
   *
   * @return integer
   * The motivation code history
   */
  public function getMotivationCodeHistory();

  /**
   * Add the motivation code to the motivation code history.
   *
   * @param $motivation_code
   *  The motivation code history
   * @return Message
   */
  public function addMotivationCodeHistory($motivation_code);

  /**
   * Clears all tracking parameters.
   *
   * @return Message
   */
  public function clearTrackingParameter();

  /**
   * Get the designation code from the form submit.
   *
   * @return integer
   *   The designation code
   */
  public function getDesignationCode();

  /**
   * Set the designation code from the form submit.
   *
   * @param $designation_code
   *  The designation code
   *
   * @return Message
   */
  public function setDesignationCode($designation_code);


  /**
   * Get the additional tracking from the form submit.
   *
   * @return string
   *   The additional tracking
   */
  public function getAdditionalTracking();

  /**
   * Set the additional tracking from the form submit.
   *
   * @param $additional_tracking
   *  The additional tracking
   *
   * @return Message
   */
  public function setAdditionalTracking($additional_tracking);

  /**
   * Set Message Reference from Entity
   * Bundles Message Entity's
   *
   * @param $entity_id
   *  The entity id.
   * @return Message
   */
  public function setReference($entity_id);

  /**
   * Returns the referenced Entity
   *
   * @return Message
   */
  public function getReferencedEntity();

  /**
   * Returns the Entity children
   *
   * @return array
   */
  public function getEntityChildren();

}
