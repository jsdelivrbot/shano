<?php
/**
 * User: MoeVoe
 * Date: 30.04.16
 * Time: 21:26
 */

namespace Drupal\ivision\iVisionController;


/**
 * Class IVision
 * @package iVisionController
 */
class IVisionBase
{

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
  public static function setConnection($connection)
  {
    self::$connection = $connection;
  }

  /**
   * @param mixed $localdata
   */
  public static function activeLocaldata($localdata = TRUE)
  {
    self::$localdata = $localdata;
  }

  /**
   * @return array
   */
  protected static function getSiteID()
  {
    self::checkConnection();
    return self::$connection->getSiteID();
  }

  /**
   * @return array
   */
  protected static function getLanguage()
  {
    self::checkConnection();
    return self::$connection->getLanguage();
  }

  /**
   * @return bool
   * @throws IVisionException
   */
  private static function checkConnection()
  {
    if (self::$connection instanceof IVisionConnect) {
      return TRUE;
    } else {
      throw new IVisionException('Initialize a IVision Object');
    }
  }

  /**
   * Send the Request via curl to the World Vision IVision Webservice and returns the Result.
   * Checks if the the call is a POST or GET request and saves the HEAD information to the private $log.
   *
   * @param $method
   *  Request Type of the API call
   * @param $uri
   *  The uri of the api call
   * @param array $post_values
   *  The array with all post values for the api call.
   * @return array List of requested IVision Data.
   * List of requested IVision Data.
   * @throws IVisionException "Curl Error: XYZ": Any possible thrown curl error.
   *  "API call (URI) failed. Response Code: 400 - 500":  Returns the HTTP Code if it is not 200 or 201.
   */
  protected static function apiRequest($method, $uri, $post_values = array())
  {
    if (self::$localdata) {
      return self::localDataRequest($method, $post_values);
    } else {


      $uri = self::$connection->getUri() . "/api/" . $uri;
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $uri);

//    PANTHEON WORKAROUND
//    Enable these Lines of code if you use the Pantheon Service.

//    $sp_iVision_hostname = str_replace("https://","",$this->api_data['uri']);
//    $sp_iVision_hostname = str_replace("http://","",$this->api_data['uri']);
//    $sp_iVision_hostname = explode("/",$sp_iVision_hostname);
//    $sp_iVision_hostname = $sp_iVision_hostname[0];
//    if ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443){
//      @curl_setopt($ch, CURLOPT_RESOLVE, array($sp_iVision_hostname . ':' . PANTHEON_SOIP_FRA_HTTPS . ':127.0.0.1'));
//    }else{
//      @curl_setopt($ch, CURLOPT_RESOLVE, array($sp_iVision_hostname . ':' . PANTHEON_SOIP_FRA_HTTP . ':127.0.0.1'));
//    }

      curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
      curl_setopt($ch, CURLOPT_SSLVERSION, 3);
      curl_setopt($ch, CURLOPT_TIMEOUT, 2000);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
      if ($method == 'POST') {
        \Drupal::logger('ivision')->notice(json_encode($post_values, JSON_HEX_APOS | JSON_UNESCAPED_UNICODE));
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_values, JSON_HEX_APOS | JSON_UNESCAPED_UNICODE));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
      }
      $curl_result = curl_exec($ch);
      $curl_header = curl_getinfo($ch);

      if (curl_errno($ch)) {
        throw new IVisionException('Curl Error: ' . curl_error($ch));
      }

      curl_close($ch);

      // Saves the HEAD Request info to the $log
      self::$log = $curl_header;

      if ($curl_header['http_code'] != 200 && $curl_header['http_code'] != 201) {
        \Drupal::logger('ivision')->notice('API call (' . $uri . ') failed. Response Code: ' . $curl_header['http_code'] . ' - ' . $curl_result);
        throw new IVisionException('API call (' . $uri . ') failed. Response Code: ' . $curl_header['http_code'] . ' - ' . $curl_result);
      }


      // Fix to Return the Data always in the same structure.
      //    if (substr($curl_result, 0, 1) == '[') {
      //      return json_decode($curl_result, TRUE);
      //    }
      //    else {
      //      $results = json_decode($curl_result, TRUE);
      //      return array($results);
      //    }

//    echo $curl_result;
//    echo json_decode($curl_result, TRUE);
//    echo json_last_error_msg();

      json_decode($curl_result, TRUE);
      if (json_last_error() == JSON_ERROR_NONE) {
        return json_decode($curl_result, TRUE);
      } else {
        return array($curl_result);
      }
    }
  }

  /**
   * Returns local data from local json files. POST requests will write custom json files.
   * Custom json files will be used if they exist (for example: apiCallName.custom.json).
   *
   * @param $method
   * POST or GET request.
   * @param $post_values
   * @return array List of requested IVision dummy data.
   * List of requested IVision dummy data.
   * @throws IVisionException
   */
  private static function localDataRequest($method, $post_values)
  {
    // builds $path and $filename variables.
    $data = '';
    $path = dirname(__FILE__) . "/localdata/";
    $filename = debug_backtrace()[2]['function'];

    $filename_custom = $filename . '.custom.json';
    $filename .= '.json';

    // If custom json files exist, they will be taken as default data.
    if (file_exists($path . $filename_custom)) {
      $filename = $filename_custom;
    }

    // read the json data.
    $file_src = fopen($path . $filename, 'r');
    if ($file_src === FALSE) {
      throw new IVisionException('Error: Could not open file: ' . $filename);
    }


    if (strtoupper($method) == 'GET') {
      // If it is a GET request the data will be returned.
      $data = fread($file_src, filesize($path . $filename));
    } elseif (strtoupper($method) == 'POST') {
      // If it is a POST request the old json file and the new data will be merged.
      $data = fread($file_src, filesize($path . $filename));
      $data = json_decode($data, TRUE);
      $data = array_replace_recursive($data, $post_values);
      fclose($file_src);

      // The merged data will be written in the custom json file.
      $file_src = fopen($path . $filename_custom, 'w');
      if ($file_src === FALSE) {
        throw new IVisionException('Error: Could not open or create file: ' . $filename);
      }
      if (!defined('JSON_PRETTY_PRINT')) {
        define('JSON_PRETTY_PRINT', 0);
      }
      fwrite($file_src, json_encode($data, JSON_PRETTY_PRINT));
      fclose($file_src);

      $data = TRUE;
    }
    self::$log = array('dummyData' => TRUE);

    // Fix to Return the Data always in the same structure.
    if (substr($data, 0, 1) == '[') {
      return json_decode($data, TRUE);
    } else {
      $results = json_decode($data, TRUE);
      return $results;
    }
  }

}
