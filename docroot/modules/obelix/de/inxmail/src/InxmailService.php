<?php

namespace Drupal\inxmail;
use Drupal\Core\Url;

/**
 * Class InxmailService.
 *
 * @package Drupal\inxmail
 */
class InxmailService implements InxmailServiceInterface
{
  static protected $inxmail_config = array();

  /**
   * Constructor.
   *
   */
  public function __construct()
  {
    self::$inxmail_config = \Drupal::configFactory()->get('inxmail.config');
  }


  /**
   * Send a http request to inxmail and write all newsletter data.
   *
   * @param array $args
   * possible keys
   *  geschlecht : gender of the user - f or m,
   *  vorname : name of the user - string
   *  nachname : surname of the user - string
   *  email : email of the user - string
   *  INXMAIL_SUBSCRIPTION(optional) : default is newsletter - string
   */
  public function send(array $args) {

    $config_sources = self::$inxmail_config;
    $values = $config_sources->get('inxmail');

    $path = '/Worldvision/subscription/servlet';

    if(!isset($args['INXMAIL_SUBSCRIPTION'])){
      $args['INXMAIL_SUBSCRIPTION'] = 'newsletter';
    }

    // absolute URL's
    $return_url = Url::fromUserInput($values['return_url'], ['absolute' => TRUE, $_SERVER['REQUEST_SCHEME'] => TRUE]);
    $error_url = Url::fromUserInput($values['error_url'], ['absolute' => TRUE, $_SERVER['REQUEST_SCHEME'] => TRUE]);

    $args['INXMAIL_HTTP_REDIRECT'] = $return_url->toString();
    $args['INXMAIL_HTTP_REDIRECT_ERROR'] = $error_url->toString();

    $parameters = http_build_query($args);

    // Send http request to inxmail.
    $client = \Drupal::httpClient();
    $uri = '//' . $values['domain'] . $path . '?' . $parameters;
    try {
      $response = $client->get($uri);
    }
    catch (RequestException $e) {
      watchdog_exception('inxmail', $e);
    }
  }

}
