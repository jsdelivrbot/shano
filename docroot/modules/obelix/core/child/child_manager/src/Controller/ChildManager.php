<?php

namespace Drupal\child_manager\Controller;

use Drupal\child\Entity\Child;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\Entity;
use Drupal\Core\Field\EntityReferenceFieldItemList;
use Drupal\datasourcehandler\Datasource;
use Drupal\file\Entity\File;
use Drupal\image\Plugin\Field\FieldType\ImageItem;
use Drupal\project\Entity\Project;
use Drupal\user\Entity\User;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\datasourcehandler\DatasourceService;
use Zend\Diactoros\Response;

/**
 * Class ChildManager.
 *
 * @package Drupal\child_manager\Controller
 */
class ChildManager extends ControllerBase {

  /**
   * Drupal\datasourcehandler\DatasourceService definition.
   *
   * @var Datasource
   */
  protected $datasource;

  /**
   * {@inheritdoc}
   * @param $datasource DatasourceService
   */
  public function __construct(DatasourceService $datasource) {
    $this->datasource = $datasource->get('default');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('datasource')
    );
  }

  /**
   * updateChildDB.
   *
   * @return string Child DB update message.
   * Returns the number of Child Entity's created, deleted and changed.
   */
  public function updateChildDB() {

    $child_entity_type = $this->entityTypeManager()->getStorage('child');
    $children = $this->datasource->getChildClass()->getChildrenForSponsorship();

    \Drupal::logger('child_manager')
      ->notice('Update children: new-' . count($children['new']).' updated-' . count($children['updated']).' deleted-' . count($children['deleted']). ' info: '.var_export($children['info'],TRUE));

    // insert child entity's.
    foreach ($children['new'] as $child) {
      /** @var \Drupal\child\Entity\Child $child */
      $child_entity_type->save($child);
    }

    // update child entity's.
    foreach ($children['updated'] as $child) {
      $this->updateChild($child);
    }

    $child_entity_type->delete($children['deleted']);


    foreach ($children as $type => $children_by_type) {
      if($type != 'info'){
        foreach ($children_by_type as $key => $child) {
          /** @var Child $child */
          $children[$type][$key] =
            [
              'ivision_id' => $child->get('ivision_id')->value,
              'field_child_givenname' => $child->get('field_child_givenname')->value,
              'field_child_familyname' => $child->get('field_child_familyname')->value,
            ];
        }
      }

    }

    return [
      '#theme' => 'child_manager',
      '#output' => $children,
    ];
  }

  /**
   * clearChildDB.
   *
   * Deletes all Child Entity's
   * @return string success message.
   */
  public function deleteChild() {

    $child_entity_type = $this->entityTypeManager()->getStorage('child');
    $child_entitys = $child_entity_type->loadMultiple();
    $child_entity_type->delete($child_entitys);

    return [
      '#type' => 'markup',
      '#markup' => $this->t('Cleared child database.'),
    ];
  }

  /**
   * clearChildDB.
   *
   * Deletes all Child Entity's
   * @return string success message.
   */
  public function clearChildDB() {

    $child_entity_type = $this->entityTypeManager()->getStorage('child');
    $child_entitys = $child_entity_type->loadMultiple();
    $child_entity_type->delete($child_entitys);

    return [
      '#type' => 'markup',
      '#markup' => $this->t('Cleared child database.'),
    ];
  }

  /**
   * Compares a array data set with an existing Child entity and
   * returns TRUE or FALSE if the data is equal.
   *
   * @param array $data
   *  Data array to be compared with Child.
   * @param Child $child
   *  Holds the Child to be checked.
   * @return bool
   *  Returns if the array data and the Child is equal.
   */
  public static function equalChildDataCheck($data, Child $child) {
    foreach ($data as $key => $value) {
      switch ($key) {
        case 'field_child_project' :
          /** @var EntityReferenceFieldItemList $entity_reference */
          $entity_reference = $child->get($key);
          $project_list = $entity_reference->referencedEntities();

          /** @var Project $project */
          $project = $project_list[0];
          $child_value = $project->get('project_id')->value;
          break;
        default:
          $child_value = $child->get($key)->value;
      }

      if ($child_value != $value) {
        return FALSE;
      }
    }
    return TRUE;
  }

  /**
   * Unblock all free Child entity's
   *
   * @return array
   * Returns the number of unblocked children.
   */
  public function unblockChildren() {
    $child_entity_type = $this->entityTypeManager()->getStorage('child');
    $child_entitys = $child_entity_type->loadByProperties(['status' => 2]);

    $counter = 0;

    foreach ($child_entitys as $child) {
      /** @var Child $child */
      if ($child->getBlockTime()->value < time()) {
        $child->setStatus(1);
        $child->setBlockTime(0);
        $child->setBlockedFrom(NULL);
        $child->save();
        $counter++;
      }
    }
    return [
      '#type' => 'markup',
      '#markup' => $this->t('Unblocked %counter children.', ['%counter' => $counter]),
    ];
  }

  /**
   * Unblock Sponsored children.
   * After 6 weeks sponsored children are unblocked.
   * Security reason to be sure that the children could be deleted.
   *
   * @return array
   * Returns the number of unblocked children.
   */
  public function unblockSponsoredChildren() {
    $child_entity_type = $this->entityTypeManager()->getStorage('child');
    $child_entitys = $child_entity_type->loadByProperties(['status' => 0]);

    $counter = 0;

    // 6 weeks
    $offset = 3628800;

    foreach ($child_entitys as $child) {
      /** @var Child $child */
      if ($child->getBlockTime()->value < (time() - $offset)) {
        $child->setBlockTime(0);
        $child->setBlockedFrom(NULL);
        $child->save();
        $counter++;
      }
    }
    return [
      '#type' => 'markup',
      '#markup' => $this->t('Unblocked %counter sponsored children.', ['%counter' => $counter]),
    ];
  }

  /**
   * Updates all given data from $child with data in DB.
   *
   * @param $child Child
   */
  public function updateChild($child) {
    $child_entity_type = $this->entityTypeManager()->getStorage('child');
    /** @var Child $child_db */
    $child_db = $child_entity_type->load($child->getIVisionID());

    $fields_exclude = [
      'field_child_image',
      'field_child_project',
      'field_child_countries_description',
      'field_child_schoolsubjects_description',
    ];
    foreach ($child->getFields() as $field_key => $field_value) {
      if (strpos($field_key, 'field_') === 0 && !in_array($field_key, $fields_exclude)) {
        $child_db->set($field_key, $field_value->value);
      }
    }
    $child_db->save();
  }

}
