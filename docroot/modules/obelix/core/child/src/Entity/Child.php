<?php

namespace Drupal\child\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\child\ChildInterface;
use Drupal\Core\Field\EntityReferenceFieldItemList;
use Drupal\Core\Field\FieldItemList;
use Drupal\country\Entity\Country;
use Drupal\project\Entity\Project;
use Drupal\user\UserInterface;

/**
 * Defines the Child entity.
 *
 * @ContentEntityType(
 *   id = "child",
 *   label = @Translation("Child"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\child\ChildListBuilder",
 *     "views_data" = "Drupal\child\Entity\ChildViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\child\Form\ChildForm",
 *       "add" = "Drupal\child\Form\ChildForm",
 *       "edit" = "Drupal\child\Form\ChildForm",
 *       "delete" = "Drupal\child\Form\ChildDeleteForm",
 *     },
 *     "access" = "Drupal\child\ChildAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\child\ChildHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "child",
 *   admin_permission = "administer child entities",
 *   entity_keys = {
 *     "id" = "ivision_id",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "status" = "status",
 *     "block_time" = "block_time",
 *     "blocked_from" = "blocked_from",
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/child/{child}",
 *     "add-form" = "/admin/structure/child/add",
 *     "edit-form" = "/admin/structure/child/{child}/edit",
 *     "delete-form" = "/admin/structure/child/{child}/delete",
 *     "collection" = "/admin/structure/child",
 *     "import" = "/child_manager/updateChildDB",
 *     "unblock" = "/child_manager/unblockChildren"
 *   },
 *   field_ui_base_route = "child.settings"
 * )
 */
class Child extends ContentEntityBase implements ChildInterface
{
  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values)
  {
    parent::preCreate($storage_controller, $values);
    $values += array(
      'user_id' => \Drupal::currentUser()->id(),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getIVisionID()
  {
    return $this->get('ivision_id')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setIVisionID($ivision_id)
  {
    $this->set('ivision_id', $ivision_id);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime()
  {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp)
  {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner()
  {
    return $this->get('user_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId()
  {
    return $this->get('user_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid)
  {
    $this->set('user_id', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account)
  {
    $this->set('user_id', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function isPublished()
  {
    return (bool)$this->getEntityKey('status');
  }

  /**
   * {@inheritdoc}
   */
  public function setPublished($published)
  {
    $this->set('status', $published ? NODE_PUBLISHED : NODE_NOT_PUBLISHED);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCountry()
  {
    // Check project existence.
    if (!$this->getproject()) {
      return NULL;
    }

    /** @var EntityReferenceFieldItemList $entity_reference */
    $entity_reference = $this->getproject()->get('field_country');
    return $entity_reference->referencedEntities()[0];
  }

  /**
   * {@inheritdoc}
   */
  public function getproject()
  {
    /** @var EntityReferenceFieldItemList $entity_reference */
    $entity_reference = $this->get('field_child_project');
    $project = $entity_reference->referencedEntities()[0];
    return $project;
  }

  /**
   * {@inheritdoc}
   */
  public function getFamilyName()
  {
    return $this->get('field_child_familyname');
  }

  /**
   * {@inheritdoc}
   */
  public function getGivenName()
  {
    return $this->get('field_child_givenname');
  }

  /**
   * {@inheritdoc}
   */
  public function getGenderDesc()
  {
      return $this->get('field_child_genderdesc')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getBirthdate()
  {
    return $this->get('field_child_birthdate');
  }

  /**
   * {@inheritdoc}
   */
  public function getSequenceNumber()
  {
    return str_pad((int)$this->get('field_child_childsequence')->value, 4, 0, STR_PAD_LEFT);
  }

  /**
   * {@inheritdoc}
   */
  public function getUniqueSequenceNumber()
  {
    if (!$this->getproject()) {
      return NULL;
    }

    return $this->getproject()->getProjectID() . '-' . $this->getSequenceNumber();
  }

  /**
   * @inheritDoc
   */
  public function setStatus($status)
  {
    $this->set('status', $status);
    return $this;
  }

  /**
   * @inheritDoc
   */
  public function getStatus()
  {
    return $this->get('status')->value;
  }


  /**
   * @inheritDoc
   */
  public function setBlockTime($timestamp)
  {
    $this->set('block_time', $timestamp);
    return $this;
  }

  /**
   * @inheritDoc
   */
  public function getBlockTime()
  {
    return $this->get('block_time');
  }

  /**
   * @inheritDoc
   */
  public function blockChildInForm()
  {
    $config = \Drupal::config('wv_site.settings');

    // Avoid children blocking at all.
    if ($disable_form_block = $config->get('disable_children_form_block')) {
      return $this;
    }

    // Let admins manage form blocking time.
    if (!$form_block_timeout = $config->get('children_form_block_timeout')) {
      $form_block_timeout = 1800;
    }

    // Block time for forms by default is 30 minutes.
    $this->setBlockTime(time() + $form_block_timeout);
    $this->setStatus(2);
    $this->setBlockedFrom('form');
    $this->save();

    return $this;
  }

  /**
   * @inheritDoc
   */
  public function blockChildForSuggestion()
  {
    // block time for forms 2 weeks
    $this->setBlockTime(time() + 1209600);
//    $this->setBlockTime(time() + 60);
    $this->setStatus(2);
    $this->save();
    return $this;
  }

  /**
   * @inheritDoc
   */
  public function getAge()
  {
    $date_service = \Drupal::service('date.formatter');
    $age = $date_service->format(time(), 'custom', 'Y') - $date_service->format(strtotime($this->getBirthdate()->value), 'custom', 'Y');
    //if birthday didn't occur that last year, then decrement
    if ($date_service->format(time(), 'custom', 'z') < $date_service->format(strtotime($this->getBirthdate()->value), 'custom', 'z')) $age--;
    return $age;
  }

  /**
   * @inheritDoc
   */
  public function setBlockedFrom($blocked_from)
  {
    $this->set('blocked_from', $blocked_from);
    return $this;
  }

  /**
   * @inheritDoc
   */
  public function getBlockedFrom()
  {
    return $this->get('blocked_from');
  }

  /**
   * @inheritDoc
   */
  public function setFoundSponsorship()
  {
    $this->setStatus(0);
    return $this;
  }

  /**
   * @inheritDoc
   */
  public function setChildVideoUrl($child_video_url)
  {
    $this->set('field_child_video_url', $child_video_url);
    return $this;
  }

  /**
   * @inheritDoc
   */
  public function getChildVideoUrl()
  {
    return $this->get('field_child_video_url');
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type)
  {
    $fields['ivision_id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('iVision ID'))
      ->setDescription(t('The iVision ID of the Child entity.'))
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'number',
        'weight' => 0,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'number',
        'weight' => 0,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The UUID of the Child entity.'))
      ->setReadOnly(TRUE);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user ID of author of the Child entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setDefaultValueCallback('Drupal\node\Entity\Node::getCurrentUserId')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', array(
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 0,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'entity_reference_autocomplete',
        'weight' => 5,
        'settings' => array(
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ),
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['status'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Publishing status'))
      ->setDescription(t('A integer indicating whether the Child is published.'))
      ->setDefaultValue(TRUE)
      ->setSetting('size', 'tiny');

    $fields['langcode'] = BaseFieldDefinition::create('language')
      ->setLabel(t('Language code'))
      ->setDescription(t('The language code for the Child entity.'))
      ->setDisplayOptions('form', array(
        'type' => 'language_select',
        'weight' => 10,
      ))
      ->setDisplayConfigurable('form', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    $fields['block_time'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Block time'))
      ->setDescription(t('The time the Child is blocked.'))
      ->setDefaultValue(0);

    $fields['blocked_from'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Blocked from'))
      ->setDescription(t('The blocked reason. Could be an email address if child is blocked by suggestion.'))
      ->setTranslatable(TRUE)
      ->setSetting('max_length', 128);

    return $fields;
  }
}
