<?php
/**
 * User: MoeVoe
 * Date: 04.06.16
 * Time: 22:00
 */

namespace Drupal\ivision_nav16;

use Drupal\child\Entity\Child;
use Drupal\child_manager\Controller\ChildManager;
use Drupal\Core\Session\SessionManager;
use Drupal\country_manager\Controller\CountryManager;
use Drupal\datasourcehandler\DatasourceChildInterface;
use Drupal\file\Entity\File;
use Drupal\ivision_nav16\iVisionController\IVisionException;
use Drupal\ivision_nav16\iVisionController\IVisionLogger;
use Drupal\project_manager\Controller\ProjectManager;
use Drupal\ivision_nav16\iVisionController\IVisionChild as IVisionControllerChild;
use Exception;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class IVisionNAV16Child
 * @package Drupal\ivision_nav16
 */
class IVisionNAV16Child implements DatasourceChildInterface {

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
      'info' => [],
    ];

    if ($number === NULL) {
      $number = 5;
    }
    $existing_children = self::$child_entity_type->loadMultiple();
    // @todo make reservationID editable in the backend


    // get children in interval. Steps are saved in config.
    $ivision_config = \Drupal::configFactory()->getEditable('ivision.config');
    $child_update = $ivision_config->get('child_update');
    if (!$child_update) {
      $child_update = [
        'from' => 0
      ];
    }

    $response['info']['from'] = $child_update['from'];

    $logger = new IVisionLogger();
    try {
      $all_new_children = IVisionControllerChild::getChildrenByReservationID(402594, NULL, $logger);
    } catch (IVisionException $e) {
      \Drupal::logger('ivision_nav16')->error('child'.$logger->renderLogs());
      return NULL;
    }
    \Drupal::logger('ivision_nav16')->notice('child'.$logger->renderLogs());

    $response['info']['total'] = count($all_new_children);

    $new_children = array_slice($all_new_children, $child_update['from'], $number);

    // increase interval steps
    if (count($all_new_children) > $child_update['from']) {
      $child_update['from'] += $number;
      $ivision_config->set('child_update', $child_update);
    }
    else {
      $ivision_config->clear('child_update');
    }
    $ivision_config->save();

    $response['info']['to'] = $child_update['from'];

    // customize the array keys and set primary key iVision ID as key index.
    $new_children_processed = [];

    foreach ($new_children as $new_child) {
      $new_child = (array) $new_child;
      foreach ($new_child as $key => $value) {
        $new_child['field_child_' . strtolower($key)] = $value;
        unset($new_child[$key]);
      }

      //removed or renamed fields to prevent precessing problems with the Child entity
      $new_child['ivision_id'] = $new_child['field_child_id'];
      unset($new_child['field_child_id']);

      $new_child['field_child_project'] = $new_child['field_child_projectid'];
      unset($new_child['field_child_projectid']);

      $new_child['field_child_genderdesc'] = $new_child['field_child_genders_description'];
      unset($new_child['field_child_genders_description']);

      $new_child['field_child_schoolleveldesc'] = $new_child['field_child_school_levels_description'];
      unset($new_child['field_child_school_levels_description']);

      $new_child['field_child_healthdesc'] = $new_child['field_child_healthstatuses_description'];
      unset($new_child['field_child_healthstatuses_description']);

      $new_child['field_child_handicapdesc'] = $new_child['field_child_handicaps_description'];
      unset($new_child['field_child_handicaps_description']);

      $new_child['field_child_playdesc'] = $new_child['field_child_playactivities_description'];
      unset($new_child['field_child_playactivities_description']);

      $new_child['field_child_liveswithdesc'] = $new_child['field_child_liveswith_description'];
      unset($new_child['field_child_liveswith_description']);

      $new_child['field_child_motherstatusdesc'] = $new_child['field_child_motherstatuses_description'];
      unset($new_child['field_child_motherstatuses_description']);

      $new_child['field_child_motherjobstatusdesc'] = $new_child['field_child_motherjobstatuses_description'];
      unset($new_child['field_child_motherjobstatuses_description']);

      $new_child['field_child_peoplegroupdesc'] = $new_child['field_child_peoplegroups_description'];
      unset($new_child['field_child_peoplegroups_description']);

      $new_child['field_child_choredesc'] = $new_child['field_child_chores_description'];
      unset($new_child['field_child_chores_description']);

      $new_child['field_child_fatherstatusdesc'] = $new_child['field_child_fatherstatuses_description'];
      unset($new_child['field_child_fatherstatuses_description']);

      $new_child['field_child_fatherjobstatusdesc'] = $new_child['field_child_fatherjobstatuses_description'];
      unset($new_child['field_child_fatherjobstatuses_description']);

      $new_child['field_child_countrydescription'] = $new_child['field_child_countries_description'];
      unset($new_child['field_child_countries_description']);


      unset($new_child['field_child_noschoolreasons_description']);
      unset($new_child['field_child_schoolsubjects_description']);
      unset($new_child['field_child_name']);
      unset($new_child['field_child_birthdateverified']);
      unset($new_child['field_child_age']);
      unset($new_child['field_child_key']);
      unset($new_child['field_child_reservationid']);
      unset($new_child['field_child_statusid']);
      $new_children_processed[$new_child['ivision_id']] = $new_child;
    }


    // check all existing children if a child have to be deleted.
    $all_new_children_processed = [];
    foreach ($all_new_children as $all_new_child) {
      $all_new_child = (array) $all_new_child;
      $all_new_children_processed[$all_new_child['ID']] = $all_new_child;
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
      if ($type != 'deleted' && $type != 'info') {
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

    $logger = new IVisionLogger();
    try {
      $child_image = (array) IVisionControllerChild::getOneImage(($child->get('ivision_id')->value));
    } catch (IVisionException $e) {
      \Drupal::logger('ivision_nav16')->error('child image '.$child->get('ivision_id')->value.': '.$logger->renderLogs());
      return NULL;
    }
    \Drupal::logger('ivision_nav16')->notice('child image '.$child->get('ivision_id')->value.': '.$logger->renderLogs());

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
    file_put_contents($file->getFileUri(), base64_decode($child_image['PictureBLOB']));
    $file->save();

    $child->set('field_child_image', $file);
  }

  /**
   * @inheritdoc
   */
  public static function getChildVideo(Child &$child) {
    try {
      $child_media_url = IVisionControllerChild::getChildMultiMediaURL(
        $child->getUniqueSequenceNumber(),
        'CGV',
        'VID',
        0,
        'iPad'
      );
      $child_media_url = $child_media_url[0]->Child_Multimedia_URL1->Child_Multimedia_URL1[0]->StorageURL;
    } catch (Exception $e) {
      $child_media_url = '';
    }
    $child->setChildVideoUrl($child_media_url);
  }
}
