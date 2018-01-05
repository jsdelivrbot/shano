<?php
/**
 * User: MoeVoe
 * Date: 30.04.16
 * Time: 21:26
 */

namespace Drupal\ivision_nav16\iVisionController;

use Drupal\ivision_nav16\iVisionController\ntlm\Client;
use Exception;
use stdClass;


/**
 * Class IVision
 * @package iVisionController
 */
class IVisionBase {

  /**
   * Saves the last HEAD Connection Info from the IVision Webservice.
   * @var array
   */
  private static $log;

  /**
   * Holds the connection Object.
   * @var IVisionConnect
   */
  private static $connection;

  /**
   * Saves if local json fields should be used as output.
   * @var bool
   */
  private static $localdata;

  /**
   * @param IVisionConnect $connection
   */
  public static function setConnection($connection) {
    self::$connection = $connection;
  }

  /**
   * @param mixed $localdata
   */
  public static function activeLocaldata($localdata = TRUE) {
    self::$localdata = $localdata;
  }

  /**
   * @return array
   */
  protected static function getSiteID() {
    self::checkConnection();
    return self::$connection->getSiteID();
  }

  /**
   * @return array
   */
  protected static function getLanguage() {
    self::checkConnection();
    return self::$connection->getLanguage();
  }

  /**
   * @return bool
   * @throws IVisionException
   */
  private static function checkConnection() {
    if (self::$connection instanceof IVisionConnect) {
      return TRUE;
    }
    else {
      throw new IVisionException('Initialize a IVision Object');
    }
  }

  /**
   * Send the Request via curl to the World Vision IVision Webservice and returns the Result.
   *
   * @param $method
   *  Method name to get the data.
   * @param $uri
   *  The uri of the api call
   * @param array $params
   *  Additional parameters.
   * @param $result_function
   * @param IVisionLoggerInterface $logger
   * @return array List of requested IVision Data.
   * List of requested IVision Data.
   * @throws \Drupal\ivision_nav16\iVisionController\IVisionException "Curl Error: XYZ": Any possible thrown curl error.
   *  "API call (URI) failed. Response Code: 400 - 500":  Returns the HTTP Code if it is not 200 or 201.
   */
  protected static function apiRequest($method, $uri, $params = array(), $result_function, IVisionLoggerInterface &$logger = NULL) {

    // check if last "/" is missing.
    if (substr($base_uri = self::$connection->getUri(), -1, 1) != '/') {
      $base_uri .= '/';
    }
    $uri = $base_uri . rawurlencode(self::$connection->getCompanyName()) . $uri;

    if (!$logger) {
      $logger = new IVisionLogger();
    }

    // base logger information
    $logger->notice('API request', [
      'uri' => $uri,
      'method' => $method,
      'params' => $params,
    ]);

    $return = FALSE;
    try {
      $client = new Client($uri, [
        'ntlm_username' => self::$connection->getUser(),
        'ntlm_password' => self::$connection->getPassword(),
      ], $logger);
      try {
        switch ($method) {
          case 'create' :
            $create = new stdClass();
            //Define The Primary Key Values
            $incident = new stdClass();
            foreach ($params as $key => $value) {
              $incident->$key = $value;
            }
            //Create The Record
            $create->IncidentBuffer = $incident;
            $result = $client->$method($create);
            $return = $result->IncidentBuffer->Key;
            $logger->notice('Result: ', ['IncidentBuffer' => $result->IncidentBuffer]);
            break;
          case 'Read' :

            $result = $client->$method($params);
            $return = $result->$result_function;
            break;
          case 'ReadMultiple' :
            $result = $client->$method($params);
            $return = $result->ReadMultiple_Result->$result_function;
            break;
        }

      } catch (Exception $e) {
        $logger->error('SOAP error', ['exception' => $e->getMessage()]);
        throw new IVisionException('API SOAP error');
      }
    } catch (Exception $e) {
      $logger->error('Connection error', ['exception' => $e->getMessage()]);
      throw new IVisionException('API connection error');
    }
    return $return;
  }


}
