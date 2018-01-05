<?php
/**
 * User: MoeVoe
 * Date: 04.06.16
 * Time: 22:00
 */

namespace Drupal\ivision_nav16;

use Drupal\datasourcehandler\Datasource;
use Drupal\ivision_nav16\iVisionController\IVisionBase;
use Drupal\ivision_nav16\iVisionController\IVisionConnect;
use Drupal\ivision_nav16\iVisionController\IVisionException;


/**
 * Class IVisionNav16
 * @package Drupal\ivision
 */
class IVisionNav16 extends Datasource
{
  /**
   * IVisionNav16 constructor.
   * Setup Connection to iVisionController.
   * @param array $args
   * @param bool $test_data
   */
  public function __construct($args = NULL, $test_data = FALSE)
  {
    if ($args !== NULL) {
      try {
        $connection = new IVisionConnect($args['uri'], $args['companyName'], $args['user'], $args['password']);
        IVisionBase::setConnection($connection);
        if ($test_data) {
          IVisionBase::activeLocaldata();
        }
      } catch (IVisionException $e) {
        echo $e->getMessage();
      }
    }
  }

  /**
   * @inheritdoc
   */
  public function getLabel()
  {
    return [__CLASS__ => 'iVision NAV 16 source'];
  }

  /**
   * @inheritdoc
   */
  public function getArguments()
  {
    return ['uri', 'companyName', 'user', 'password'];
  }

  /**
   * @inheritdoc
   */
  public function getChildClass()
  {
    return new IVisionNAV16Child();
  }

  /**
   * @inheritdoc
   */
  public function getProjectClass()
  {
    return new IVisionNAV16Project();
  }

  /**
   * @inheritdoc
   */
  public function getIncidentClass()
  {
    return new IVisionNav16Incident();
  }


  /**
   * @inheritdoc
   * @param null $args
   * @return static
   */
  public static function create($args = null)
  {
    return new static($args);
  }


}
