<?php
/**
 * Created by PhpStorm.
 * User: Felix
 * Date: 06.06.16
 * Time: 12:10
 */

namespace Drupal\ivision_nav16\iVisionController;


class IVisionIncident extends IVisionBase implements IVisionIncidentInterface
{

  /**
   * @inheritdoc
   */
  public static function incident(array $args, IVisionLoggerInterface &$logger = NULL)
  {
    $uri = '/Page/IncidentBuffer';
    return parent::apiRequest('create', $uri, $args, 'IncidentBuffer', $logger);
  }

}
