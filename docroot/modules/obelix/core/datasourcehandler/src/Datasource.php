<?php
/**
 * User: MoeVoe
 * Date: 04.06.16
 * Time: 22:00
 */

namespace Drupal\datasourcehandler;

use Symfony\Component\EventDispatcher\Event;


/**
 * Class Datasource
 *
 * If you want to create a data source.
 * - overwrite the abstract methods
 * - create a event listener in your xxx.service.yml file
 *   example:
 *    dataSourceName.subscriber:
 *      class: Namespace\ClassName
 *      tags:
 *        - { name: event_subscriber }
 * @package Drupal\datasourcehandler
 */
// @todo could be implemented as annotation
abstract class Datasource implements DatasourceInterface
{
  /**
   * @inheritdoc
   */
  abstract public function getLabel();

  /**
   * @inheritdoc
   */
  abstract public function getArguments();

  /**
   * @inheritdoc
   */
  abstract public function getChildClass();

  /**
   * @inheritdoc
   */
  abstract public function getProjectClass();

  /**
   * @inheritdoc
   */
  abstract public function getIncidentClass();


  /**
   * @inheritdoc
   */
  public function registerClass(Event $event)
  {
    DatasourceService::registerClass($this->getLabel());
  }

  /**
   * @inheritdoc
   */
  public static function create()
  {
    return new static;
  }

  /**
   * @inheritdoc
   */
  public static function getSubscribedEvents()
  {
    $events['datasource.register_class'][] = 'registerClass';
    return $events;
  }
}
