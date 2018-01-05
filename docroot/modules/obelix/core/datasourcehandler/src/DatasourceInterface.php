<?php
/**
 * User: MoeVoe
 * Date: 04.06.16
 * Time: 22:00
 */

namespace Drupal\datasourcehandler;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


/**
 * Interface DatasourceInterface
 * @package Drupal\datasourcehandler
 */
interface DatasourceInterface extends EventSubscriberInterface
{

  /**
   * @return string
   * Namespace\Class => label of the data source.
   */
  public function getLabel();

  /**
   * @return string
   * Namespace\Class => label of the data source.
   */
  public function getArguments();

  /**
   * @return DatasourceChildInterface
   * Have to return a entity to handle the child data.
   */
  public function getChildClass();

  /**
   * @return DatasourceProjectInterface
   * Have to return a entity to handle the project data.
   */
  public function getProjectClass();

  /**
   * @return DatasourceIncidentInterface
   * Have to return a entity to handle the incident data.
   */
  public function getIncidentClass();


  /**
   * Register data source wit unique label in data source service.
   * Have to be called by event listener.
   * @param Event $event
   */
  public function registerClass(Event $event);

  /**
   * Factory method to create instance of the class.
   *
   * @return Datasource
   * Instance of the Class
   */
  public static function create();

  /**
   * @inheritdoc
   */
  public static function getSubscribedEvents();

}
