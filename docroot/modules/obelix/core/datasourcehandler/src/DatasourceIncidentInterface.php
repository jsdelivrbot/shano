<?php
/**
 * User: MoeVoe
 * Date: 04.06.16
 * Time: 22:00
 */

namespace Drupal\datasourcehandler;

/**
 * Interface DatasourceIncidentInterface
 * @package Drupal\datasourcehandler
 */
interface DatasourceIncidentInterface {

  /**
   * Sends al list of data to the datasource.
   * In the most cases this will be form data fields.
   *
   * @param array $args
   *  List of all parameters
   * @param $incident_type
   *  The type of the incident.
   * @param $web_reference_id
   *  Unique transfer number.
   * @param $form_name
   *  Name of the form
   * @param $parent_web_reference_id
   *  Unique transfer number of the parent element. (Default null)
   * @return bool
   */
  public static function setIncident(array $args, $incident_type, $web_reference_id, $form_name, $parent_web_reference_id = NULL);
}
