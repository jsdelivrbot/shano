<?php
namespace Drupal\ivision_nav16\iVisionController\ntlm;

use Exception;
use \Psr\Log\LoggerAwareInterface;
use \Psr\Log\LoggerAwareTrait;
use \Psr\Log\LoggerInterface;
use Drupal\ivision_nav16\iVisionController\ntlm\httpStream\NTLMStream;

class Client extends \SoapClient {
  use LoggerAwareTrait;
  private $options = Array();
  private $__last_request;

  /**
   *
   * @param String $url The WSDL url
   * @param array $data Soap options, it should contain ntlm_username and ntlm_password fields
   * @param LoggerAwareInterface|LoggerInterface $logger
   * @throws Exception
   * @see \SoapClient::__construct()
   */
  public function __construct($url, $data, LoggerInterface $logger = NULL) {
    if ($logger) {
      $this->setLogger($logger);
    }
    $time_start = microtime(TRUE);
    $this->options = $data;
    if (empty($data['ntlm_username']) && empty($data['ntlm_password'])) {
      parent::__construct($url, $data);
    }
    else {
      $this->use_ntlm = TRUE;
      NTLMStream::$user = $data['ntlm_username'];
      NTLMStream::$password = $data['ntlm_password'];
      stream_wrapper_unregister('http');
      if (!stream_wrapper_register('http', 'Drupal\\ivision_nav16\\iVisionController\\ntlm\\httpStream\\NTLMStream')) {
        throw new Exception("Unable to register HTTP Handler");
      }
      parent::__construct($url, $data);
      stream_wrapper_restore('http');
    }
    if (!empty($this->logger) && (($end_time = microtime(TRUE) - $time_start) > 0.1)) {
      $this->logger->debug("WSDL Timer", Array(
        "time" => $end_time,
        "url" => $url
      ));
    }
  }

  /**
   * (non-PHPdoc)
   * @see SoapClient::__doRequest()
   * @param string $request
   * @param string $location
   * @param string $action
   * @param int $version
   * @param int $one_way
   * @return mixed|string
   */
  public function __doRequest($request, $location, $action, $version, $one_way = 0) {
    $this->__last_request = $request;
    $start_time = microtime(TRUE);
    $ch = curl_init($location);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'Method: POST',
      'User-Agent: PHP-SOAP-CURL',
      'Content-Type: text/xml; charset=utf-8',
      'SOAPAction: "' . $action . '"',
    ));
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    if (!empty($this->options['ntlm_username']) && !empty($this->options['ntlm_password'])) {
      curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_NTLM);
      curl_setopt($ch, CURLOPT_USERPWD, $this->options['ntlm_username'] . ':' . $this->options['ntlm_password']);
    }
    $response = curl_exec($ch);
    if (!empty($this->logger)) {
      // Log as an error if the curl call isn't a success
      $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      $log_func = $http_status == 200 ? 'debug' : 'error';
      // Log the call
      $this->logger->$log_func("SoapCall: " . $action, array(
        "Location" => $location,
        "HttpStatus" => $http_status,
        "Request" => $request,
        "Response" => strlen($response) > 2000 ? substr($response, 0, 2000) . "..." : $response,
        "RequestTime" => curl_getinfo($ch, CURLINFO_TOTAL_TIME),
        "RequestConnectTime" => curl_getinfo($ch, CURLINFO_CONNECT_TIME),
        "Time" => microtime(TRUE) - $start_time
      ));
    }
    return $response;
  }
}
