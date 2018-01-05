<?php
/**
 * User: MoeVoe
 * Date: 04.06.16
 * Time: 22:00
 */

namespace Drupal\ivision;

use Drupal\child\Entity\Child;
use Drupal\child_manager\Controller\ChildManager;
use Drupal\Core\Session\SessionManager;
use Drupal\country_manager\Controller\CountryManager;
use Drupal\datasourcehandler\DatasourceChildInterface;
use Drupal\file\Entity\File;
use Drupal\project_manager\Controller\ProjectManager;
use Drupal\ivision\iVisionController\IVisionChild as IVisionControllerChild;
use Exception;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class IVision
 * @package Drupal\ivision
 */
class IVisionChild implements DatasourceChildInterface {

  /**
   * @var Child
   */
  private static $child_entity_type;

  /**
   * IVisionChild constructor.
   * Set the child entityTypeManager.
   */
  public function __construct() {
    self::$child_entity_type = \Drupal::entityTypeManager()
      ->getStorage('child');
  }

  /**
   * @inheritdoc
   */
  public static function getChildrenForSponsorship($number = NULL) {
    $response = [
      'new' => [],
      'updated' => [],
      'deleted' => [],
    ];

    if ($number === NULL) {
      $number = 10;
    }
    $existing_children = self::$child_entity_type->loadMultiple();
    // @todo make reservationID editable in the backend

    // get children in interval. Steps are saved in session.
    /** @var Session $session */
    $session = \Drupal::service('session');
    $child_update = $session->get('child_update');
    if (!$child_update) {
      $child_update = [
        'from' => 0,
        'to' => $number,
      ];
    }

    $all_new_children = IVisionControllerChild::getChildrenByReservationID(402594, 10000, 0);
    $new_children = array_slice($all_new_children, $child_update['from'], $child_update['to']);

    // increase interval steps
    if (count($all_new_children) > $child_update['from']) {
      $child_update['from'] += $number;
      $child_update['to'] += $number;
      $session->set('child_update', $child_update);
    }
    else {
      $session->remove('child_update');
    }
    $session->save();

    // customize the array keys and set primary key iVision ID as key index.
    $new_children_processed = [];
    foreach ($new_children as $new_child) {
      foreach ($new_child as $key => $value) {
        $new_child['field_child_' . strtolower($key)] = $value;
        unset($new_child[$key]);
      }

      //removed or renamed fields to prevent precessing problems with the Child entity
      $new_child['ivision_id'] = $new_child['field_child_ivisionid'];
      unset($new_child['field_child_ivisionid']);
      $new_child['field_child_project'] = $new_child['field_child_projectid'];
      unset($new_child['field_child_projectid']);

      unset($new_child['field_child_name']);
      unset($new_child['field_child_isbirthdateverified']);
      unset($new_child['field_child_age']);
      $new_children_processed[$new_child['ivision_id']] = $new_child;
    }

    // check all existing children if a child have to be deleted.
    $all_new_children_processed = [];
    foreach ($all_new_children as $all_new_child) {
      $all_new_children_processed[$all_new_child['iVisionID']] = $all_new_child;
    }
    /** @var Child $existing_child */
    foreach ($existing_children as $existing_child_id => $existing_child) {
      if (!array_key_exists($existing_child_id, $all_new_children_processed) && empty($existing_child->getBlockedFrom()->value)) {
        $response['deleted'][$existing_child_id] = $existing_child;
      }
    }


    // delete already existing children with same data.
    foreach ($existing_children as $ivision_id => $child_entity) {
      //country fields are not in the entity and have to be removed for equal check

      if (array_key_exists($ivision_id, $new_children_processed)) {
        $equal_check_data = $new_children_processed[$ivision_id];
        unset($equal_check_data['field_child_countrycode']);
        unset($equal_check_data['field_child_countrydescription']);

        if (ChildManager::equalChildDataCheck($equal_check_data, $child_entity)) {
          unset($new_children_processed[$ivision_id]);
        }
        else {
          $response['updated'][$ivision_id] = $new_children_processed[$ivision_id];
        }
      }
    }

    $response['new'] = array_diff_key($new_children_processed, $response['updated']);

    // cut the array to the given size
    $new_children_processed = array_slice($new_children_processed, 0, $number, TRUE);

    // convert value array in Child Entity's
    foreach ($response as $type => $children) {
      if ($type != 'deleted') {
        foreach ($children as $ivision_id => $child) {
          $datasource = \Drupal::service('datasource');

          // set the Country Entity if not already exists.
          $country_manager = new CountryManager($datasource);
          $country = $country_manager->createCountry($child['field_child_countrycode'], $child['field_child_countrydescription']);

          // set the Project Entity if not already exists.
          $project_manager = new ProjectManager($datasource);
          $child['field_child_project'] = $project_manager->createProject($child['field_child_project'], $country);

          $child = self::$child_entity_type->create($child);
          self::getChildImage($child);
          self::getChildVideo($child);
          $response[$type][$ivision_id] = $child;
        }
      }
    }

    return $response;
  }

  /**
   * @inheritdoc
   */
  public
  static function getChildImage(Child &$child) {

    $child_image = IVisionControllerChild::getChildImage($child->get('ivision_id')->value, 400, 0);
    $path = 'public://children';

    // create children image folder
    file_prepare_directory($path, FILE_CREATE_DIRECTORY);

    $filename = $child->uuid() . ".jpg";
    $file = File::create([
      'uid' => \Drupal::currentUser()->getAccount()->id(),
      'filename' => $filename,
      'uri' => $path . '/' . $filename,
      'filemime' => 'image/jpeg',
      'timestamp' => REQUEST_TIME,
      'status' => FILE_STATUS_PERMANENT,
    ]);

    $file->enforceIsNew();
    file_put_contents($file->getFileUri(), base64_decode($child_image['image']));
    $file->save();

    $child->set('field_child_image', $file);
  }

  /**
   * @inheritdoc
   */
  public static function getChildVideo(Child &$child) {
    try {
      $child_media_url = IVisionControllerChild::getChildMediaURL('CGV', 'VID', 'iPad', 0, $child->getUniqueSequenceNumber());
    } catch (Exception $e) {
      $child_media_url = '';
    }
    $child->setChildVideoUrl($child_media_url);
  }
}
