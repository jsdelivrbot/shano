<?php
/**
 * User: MoeVoe
 * Date: 08.12.16
 * Time: 16:42
 */

namespace Drupal\ab_testing;

use Drupal\Core\Url;
use Drupal\google_tag_manager\DataLayer;
use Symfony\Component\EventDispatcher\Event;

class ABTestingDataLayer extends DataLayer {

  /**
   * @inheritdoc
   */
  public function buildGTMDataLayer(Event $event) {
    /** @var ABTestingService $ab_testing_service */
    $ab_testing_service = \Drupal::service('ab_testing');
    parent::setData([
      0 => [
        'url' => Url::fromUserInput('/kinderpatenschaft'),
        'data' => [
          'user_group' => $ab_testing_service->setUserGroup(1),
        ],
      ]
    ]);
  }

  /**
   * @inheritdoc
   */
  public static function getSubscribedEvents() {
    $events['data_layer.set'][] = 'buildGTMDataLayer';
    return $events;
  }
}


