<?php
/**
 * User: MoeVoe
 * Date: 04.06.16
 * Time: 22:00
 */

namespace Drupal\google_tag_manager;

use Drupal\Core\Path\CurrentPathStack;
use Drupal\Core\Url;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class DataLayer
 *
 */
class DataLayer implements DataLayerInterface {

  protected static $data = [];

  public function buildGTMDataLayer(Event $event) {
//    self::setData([
//      0 => [
//        'url' => Url::fromUserInput('/'),
//        'data' => [
//          'test' => 'value',
//        ],
//        'options' => [],
//      ]
//    ]);
  }

  /**
   * @inheritdoc
   */
  public function setData($data) {
    foreach ($data as $item) {
      if (isset($item['url'])) {
        $temp = [];
        /** @var Url $url */
        $url = $item['url'];
        $url_string = $url->toString();
        $temp['url'] = $url;
        if (isset($item['type'])) {
          $temp['type'] = $item['type'];
        }
        if (isset($item['selector'])) {
          $temp['selector'] = $item['selector'];
        }
        foreach ($item['data'] as $gtm_data_key => $gtm_data_value) {
          $temp['data'][$gtm_data_key] = $gtm_data_value;
        }
        // check if gtm data block already exists
        $found = FALSE;
        if(isset(self::$data[$url_string])){
          foreach (self::$data[$url_string] as $gtm_data) {
            if($gtm_data == $temp) $found = TRUE;
          }
        }

        if(!$found) {
          self::$data[$url_string][] = $temp;
        }
      }
      else {
        $temp = [
          'data' => $item['data'],
          'options' => $item['options'],
        ];
        if (isset($item['type'])) {
          $temp['type'] = $item['type'];
        }
        if (isset($item['selector'])) {
          $temp['selector'] = $item['selector'];
        }
        // check if gtm data block already exists
        $found = FALSE;
        foreach (self::$data as $gtm_data_item) {
          foreach ($gtm_data_item as $gtm_data){
            if($gtm_data == $temp) $found = TRUE;
          }
        }
        if(!$found) {
          self::$data[][] = $temp;
        }
      }

    }
  }

  /**
   * @inheritdoc
   */
  public static function getInitialData() {

    $return = [];
    foreach (self::$data as $url => $data_item) {
      foreach ($data_item as $item_key => $item) {
        if (!isset($item['type']) || $item['type'] == 'initial') {
          $return[$url][] = $item;
        }
      }
    }

    return $return;
  }

  public static function getEventData() {
    $return = [];
    foreach (self::$data as $url => $data_item) {
      foreach ($data_item as $item_key => $item) {
        if (isset($item['type']) && $item['type'] == 'event') {
          $return[$url][] = $item;
        }
      }
    }

    return $return;
  }

  /**
   * @inheritdoc
   */
  public static function create() {
    return new static;
  }

  /**
   * @inheritdoc
   */
  public static function getSubscribedEvents() {
    $events['data_layer.set'] = 'buildGTMDataLayer';
    return $events;
  }

  /**
   * @inheritdoc
   */
  public static function checkOptions($gtm_data) {
    $result = FALSE;
    if (isset($gtm_data[0]['options']) && is_array($gtm_data[0]['options'])) {
      foreach ($gtm_data[0]['options'] as $option => $option_value) {
        switch ($option) {
          case 'content_type':
            if ($node = \Drupal::routeMatch()->getParameter('node')) {
              if ($option_value == $node->bundle()) {
                $result = TRUE;
              }
            }
            break;
          case 'exception_url':
            /** @var Url $exception_url */
            foreach ($option_value['url'] as $exception_url) {
              /** @var CurrentPathStack $current_path */
              $current_path = \Drupal::service('path.current')->getPath();
              $current_url = Url::fromUserInput($current_path)->toString();
              if ($current_url == $exception_url->toString()) {
                return FALSE;
              }
            }
            break;
        }
      }
    }
    return $result;
  }
}
