<?php

/**
 * @file
 * Contains \Drupal\forms_suite\Entity\Message.
 */

namespace Drupal\forms_suite\Entity;

use Drupal\affiliate\AffiliateItemInterface;
use Drupal\affiliate\Plugin\Field\FieldType\TrackingItem;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\forms_suite\MessageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Defines the forms message entity.
 *
 * @ingroup forms_message
 *
 * @ContentEntityType(
 *   id = "forms_message",
 *   label = @Translation("Forms message"),
 *   handlers = {
 *     "access" = "Drupal\forms_suite\FormsMessageAccessControlHandler",
 *     "view_builder" = "Drupal\forms_suite\MessageViewBuilder",
 *     "views_data" = "Drupal\forms_suite\Entity\MessageViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\forms_suite\MessageForm"
 *     },
 *   },
 *   base_table = "message",
 *   admin_permission = "administer forms",
 *   entity_keys = {
 *     "id" = "id",
 *     "bundle" = "form",
 *     "uuid" = "uuid",
 *     "langcode" = "langcode"
 *   },
 *   bundle_entity_type = "form",
 *   field_ui_base_route = "entity.form.edit_form",
 * )
 */
class Message extends ContentEntityBase implements MessageInterface {
  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
  }

  /**
   * {@inheritdoc}
   */
  public function isPersonal() {
    return $this->bundle() == 'personal';
  }

  /**
   * {@inheritdoc}
   */
  public function getForm() {
    return $this->get('form')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getPersonalRecipient() {
    if ($this->isPersonal()) {
      return $this->get('recipient')->entity;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setExternalPayment($flag) {
    $this->set('external_payment', $flag);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getTransferID() {
    return $this->get('transfer_id')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setTransferID($transfer_id) {
    $this->set('transfer_id', $transfer_id);
    return $this;
  }

  /**
   * @inheritDoc
   */
  public function getMotivationCode() {
    return $this->get('motivation_code')->value;
  }

  /**
   * @inheritDoc
   */
  public function setMotivationCode($motivation_code) {
    if (!empty($motivation_code)) {
      $this->addMotivationCodeHistory($motivation_code);
      $this->set('motivation_code', $motivation_code);
      return $this;
    }
  }

  /**
   * @inheritDoc
   */
  public function getMotivationCodeHistory() {
    return $this->get('motivation_code_history')->value;
  }

  /**
   * @inheritDoc
   */
  public function addMotivationCodeHistory($motivation_code) {
    $motivation_code_history = $this->getMotivationCodeHistory();
    if (!empty($motivation_code_history)) {
      $motivation_code_history .= ', ' . $motivation_code;
    }
    else {
      $motivation_code_history = $motivation_code;
    }
    $this->set('motivation_code_history', $motivation_code_history);
    return $this;
  }

  /**
   * @inheritDoc
   */
  public function clearTrackingParameter() {
    $this->set('motivation_code', '');
    $this->set('motivation_code_history', '');
    $this->set('designation_code', '');
    $this->set('additional_tracking', '');

    return $this;
  }

  public function setAffiliateID($affiliate_id) {
    $this->set('affiliate_id', $affiliate_id);
    return $this;
  }

  /**
   * @inheritDoc
   */
  public function setReference($entity_id) {
    $this->set('entity_reference', $entity_id);
    return $this;
  }

  /**
   * @inheritDoc
   */
  public function getReferencedEntity() {
    $entity_reference = $this->get('entity_reference')->value;
    return $this->entityTypeManager()
      ->getStorage('forms_message')
      ->load($entity_reference);
  }

  /**
   * @inheritDoc
   */
  public function getEntityChildren() {
    return $this->entityTypeManager()->getStorage('forms_message')
      ->loadByProperties(['entity_reference' => $this->id()]);
  }


  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields['id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('ID'))
      ->setDescription(t('The ID of the message.'))
      ->setReadOnly(TRUE);

    $fields['form'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Form ID'))
      ->setDescription(t('The ID of the associated form.'))
      ->setSetting('target_type', 'form')
      ->setRequired(TRUE);

    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The message UUID.'))
      ->setReadOnly(TRUE);

    $fields['transfer_id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Transfer ID'))
      ->setDescription(t('The transfer ID from the webservice'))
      ->setReadOnly(TRUE)
      ->setDefaultValue(NULL);


    $fields['langcode'] = BaseFieldDefinition::create('language')
      ->setLabel(t('Language'))
      ->setDescription(t('The message language code.'))
      ->setDisplayOptions('form', array(
        'type' => 'language_select',
        'weight' => 2,
      ));

    $fields['recipient'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Recipient ID'))
      ->setDescription(t('The ID of the recipient user for personal forms messages.'))
      ->setSetting('target_type', 'user');

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    $fields['external_payment'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('External Payment'))
      ->setDescription(t('Status of the External Payment 0 none, 1 success, 2 open'))
      ->setDefaultValue(0);

    $fields['motivation_code'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Motivation code'))
      ->setDescription(t('Motivation code of the form submit.'))
      ->setDefaultValue(0);

    $fields['motivation_code_history'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Motivation code history'))
      ->setDescription(t('Motivation code history of the form submit.'))
      ->setSetting('max_length', 255)
      ->setDefaultValue(0);

    $fields['designation_code'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Designation code'))
      ->setDescription(t('Designation code of the form submit.'));

    $fields['additional_tracking'] = BaseFieldDefinition::create('text')
      ->setLabel(t('additional tracking'))
      ->setDescription(t('Additional tracking of the form submit.'));

    $fields['affiliate_id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Affiliate ID'))
      ->setDescription(t('Affiliate ID'))
      ->setDefaultValue(0);

    $fields['entity_reference'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Entity reference'))
      ->setDescription(t('Entity reference'))
      ->setDefaultValue(0);

    return $fields;
  }

  /**
   * Get the designation code from the form submit.
   *
   * @return integer
   *   The designation code
   */
  public function getDesignationCode() {
    if (!$this->get('designation_code')->isEmpty()) {
      $value = $this->get('designation_code')->getValue();
      return $value[0]['value'];
    }
    return NULL;
  }

  /**
   * Set the designation code from the form submit.
   *
   * @param $designation_code
   *  The designation code
   *
   * @return Message
   */
  public function setDesignationCode($designation_code) {
    $this->set('designation_code', $designation_code);

    return $this;
  }

  /**
   * Get the additional tracking from the form submit.
   *
   * @return string
   *   The additional tracking
   */
  public function getAdditionalTracking() {
    if (!$this->get('additional_tracking')->isEmpty()) {
      $value = $this->get('additional_tracking')->getValue();
      return $value[0]['value'];
    }
    return NULL;
  }

  /**
   * Set the additional tracking from the form submit.
   *
   * @param $additional_tracking
   *  The additional tracking
   *
   * @return Message
   */
  public function setAdditionalTracking($additional_tracking) {
    $this->set('additional_tracking', $additional_tracking);

    return $this;
  }
}
