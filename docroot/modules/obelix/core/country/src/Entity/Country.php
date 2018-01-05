<?php

namespace Drupal\country\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\EntityReferenceFieldItemList;
use Drupal\country\CountryInterface;
use Drupal\taxonomy\Entity\Term;
use Drupal\user\UserInterface;

/**
 * Defines the Country entity.
 *
 * @ingroup country
 *
 * @ContentEntityType(
 *   id = "country",
 *   label = @Translation("Country"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\country\CountryListBuilder",
 *     "views_data" = "Drupal\country\Entity\CountryViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\country\Form\CountryForm",
 *       "add" = "Drupal\country\Form\CountryForm",
 *       "edit" = "Drupal\country\Form\CountryForm",
 *       "delete" = "Drupal\country\Form\CountryDeleteForm",
 *     },
 *     "access" = "Drupal\country\CountryAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\country\CountryHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "country",
 *   admin_permission = "administer country entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "status" = "status",
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/country/{country}",
 *     "add-form" = "/admin/structure/country/add",
 *     "edit-form" = "/admin/structure/country/{country}/edit",
 *     "delete-form" = "/admin/structure/country/{country}/delete",
 *     "collection" = "/admin/structure/country",
 *   },
 *   field_ui_base_route = "country.settings"
 * )
 */
class Country extends ContentEntityBase implements CountryInterface {
  use EntityChangedTrait;
  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += array(
      'user_id' => \Drupal::currentUser()->id(),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getCountryCode() {
    return $this->get('id')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCountryCode($code) {
    $this->set('id', $code);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->get('name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setName($name) {
    $this->set('name', $name);
    return $this;
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
  public function getOwner() {
    return $this->get('user_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('user_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('user_id', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('user_id', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function isPublished() {
    return (bool) $this->getEntityKey('status');
  }

  /**
   * {@inheritdoc}
   */
  public function setPublished($published) {
    $this->set('status', $published ? NODE_PUBLISHED : NODE_NOT_PUBLISHED);
    return $this;
  }

  /**
   * @inheritDoc
   */
  public function getContinent()
  {
    /** @var EntityReferenceFieldItemList $continent */
    if($continent = $this->get('field_continent')->target_id){
      $continent = Term::load($continent)->getName();
    }
    return $continent;
  }

  /**
   * {@inheritdoc}
   */
  public function getSpecialCountryArticle() {
    // Avoid fatals for countries sites where this field is not available.
    return $this->hasField('field_special_country_article')
      ? $this->get('field_special_country_article')->value : NULL;
  }



  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields['id'] = BaseFieldDefinition::create('string')
        ->setLabel(t('Country Code'))
        ->setDescription(t('The Country Code of the Country entity.'))
        ->setSettings(array(
            'max_length' => 4,
            'text_processing' => 0,
        ))
        ->setDisplayOptions('view', array(
            'label' => 'above',
            'type' => 'string',
            'weight' => -3,
        ))
        ->setDisplayOptions('form', array(
            'type' => 'string_textfield',
            'weight' => -3,
        ))
        ->setDisplayConfigurable('form', TRUE)
        ->setDisplayConfigurable('view', TRUE);

    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The UUID of the Country entity.'))
      ->setReadOnly(TRUE);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user ID of author of the Country entity.'))
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

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the Country entity.'))
      ->setSettings(array(
        'max_length' => 50,
        'text_processing' => 0,
      ))
      ->setDefaultValue('')
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'string_textfield',
        'weight' => -4,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Publishing status'))
      ->setDescription(t('A boolean indicating whether the Country is published.'))
      ->setDefaultValue(TRUE);

    $fields['langcode'] = BaseFieldDefinition::create('language')
      ->setLabel(t('Language code'))
      ->setDescription(t('The language code for the Country entity.'))
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

    return $fields;
  }

}
