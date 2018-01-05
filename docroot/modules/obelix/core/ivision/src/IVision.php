<?php
/**
 * User: MoeVoe
 * Date: 04.06.16
 * Time: 22:00
 */

namespace Drupal\ivision;

use Drupal\datasourcehandler\Datasource;
use Drupal\ivision\iVisionController\IVisionBase;
use Drupal\ivision\iVisionController\IVisionConnect;
use Drupal\ivision\iVisionController\IVisionException;


/**
 * Class IVision
 * @package Drupal\ivision
 */
class IVision extends Datasource
{
  /**
   * IVision constructor.
   * Setup Connection to iVisionController.
   * @param array $args
   * @param bool $test_data
   */
  public function __construct($args = NULL, $test_data = FALSE)
  {
    if ($args !== NULL) {
      try {
        $connection = new IVisionConnect($args['uri'], $args['language'], $args['siteID']);
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
    return [__CLASS__ => 'iVision source'];
  }

  /**
   * @inheritdoc
   */
  public function getArguments()
  {
    return ['uri', 'language', 'siteID'];
  }

  /**
   * @inheritdoc
   */
  public function getChildClass()
  {
    return new IVisionChild();
  }

  /**
   * @inheritdoc
   */
  public function getProjectClass()
  {
    return new IVisionProject();
  }

  /**
   * @inheritdoc
   */
  public function getIncidentClass()
  {
    return new IVisionIncident();
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
