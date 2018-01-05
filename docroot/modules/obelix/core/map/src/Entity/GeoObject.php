<?php

namespace Drupal\map\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\EntityReferenceFieldItemList;
use Drupal\country\Entity\Country;
use Drupal\map\GeoObjectInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Geo object entity.
 *
 * @ingroup map
 *
 * @ContentEntityType(
 *   id = "geo_object",
 *   label = @Translation("Geo object"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\map\GeoObjectListBuilder",
 *     "views_data" = "Drupal\map\Entity\GeoObjectViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\map\Form\GeoObjectForm",
 *       "add" = "Drupal\map\Form\GeoObjectForm",
 *       "edit" = "Drupal\map\Form\GeoObjectForm",
 *       "delete" = "Drupal\map\Form\GeoObjectDeleteForm",
 *     },
 *     "access" = "Drupal\map\GeoObjectAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\map\GeoObjectHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "geo_object",
 *   admin_permission = "administer geo object entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "status" = "status",
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/geo_object/{geo_object}",
 *     "add-form" = "/admin/structure/geo_object/add",
 *     "edit-form" = "/admin/structure/geo_object/{geo_object}/edit",
 *     "delete-form" = "/admin/structure/geo_object/{geo_object}/delete",
 *     "collection" = "/admin/structure/geo_object",
 *   },
 *   field_ui_base_route = "geo_object.settings"
 * )
 */
class GeoObject extends ContentEntityBase implements GeoObjectInterface
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
    public function getName()
    {
        return $this->get('name')->value;
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->set('name', $name);
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
   * @inheritDoc
   */
  public function getJson()
  {
    return $this->get('field_google_geo_object')->getValue()[0]['geo_json'];
  }

  /**
   * @inheritDoc
   */
  public function getHighlighted()
  {
    return $this->get('field_highlighted');
  }

  /**
   * @inheritDoc
   */
  public function getCountryID()
  {
    /** @var EntityReferenceFieldItemList $country_reference */
    $country_reference = $this->get('field_country');
    if(isset($country_reference->getValue()[0])){
      return $country_reference->getValue()[0]['target_id'];
    }else{
      return NULL;
    }
  }


  /**
     * {@inheritdoc}
     */
    public static function baseFieldDefinitions(EntityTypeInterface $entity_type)
    {
        $fields['id'] = BaseFieldDefinition::create('integer')
            ->setLabel(t('ID'))
            ->setDescription(t('The ID of the Geo object entity.'))
            ->setReadOnly(TRUE);
        $fields['uuid'] = BaseFieldDefinition::create('uuid')
            ->setLabel(t('UUID'))
            ->setDescription(t('The UUID of the Geo object entity.'))
            ->setReadOnly(TRUE);

        $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
            ->setLabel(t('Authored by'))
            ->setDescription(t('The user ID of author of the Geo object entity.'))
            ->setRevisionable(TRUE)
            ->setSetting('target_type', 'user')
            ->setSetting('handler', 'default')
            ->setDefaultValueCallback('Drupal\node\Entity\Node::getCurrentUserId')
            ->setTranslatable(TRUE)
            ->setReadOnly(TRUE);

        $fields['name'] = BaseFieldDefinition::create('string')
            ->setLabel(t('Name'))
            ->setDescription(t('The name of the Geo object entity.'))
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
            ->setDescription(t('A boolean indicating whether the Geo object is published.'))
            ->setDefaultValue(TRUE);

        $fields['langcode'] = BaseFieldDefinition::create('language')
            ->setLabel(t('Language code'))
            ->setDescription(t('The language code for the Geo object entity.'))
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
