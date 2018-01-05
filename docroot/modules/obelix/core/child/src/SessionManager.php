<?php

namespace Drupal\child;

use Drupal\Core\KeyValueStore\KeyValueExpirableFactoryInterface;
use Drupal\Component\Utility\Crypt;

/**
 * Manages Session in a way that does not bypass page cache.
 */
class SessionManager {

  /**
   * The key value store to use.
   *
   * @var \Drupal\Core\KeyValueStore\KeyValueStoreInterface
   */
  protected $keyValueStore;

  /**
   * Current session id for this custom session manager.
   */
  protected $session_id;

  /**
   * Statically cached data for the session manager.
   */
  protected $cached_data = [];

  /**
   * Constructs a State object.
   *
   * @param \Drupal\Core\KeyValueStore\KeyValueExpirableFactoryInterface $storage_factory
   *   The key storage factory to use.
   */
  public function __construct(KeyValueExpirableFactoryInterface $storage_factory) {
    $this->keyValueStore = $storage_factory->get('child.session_cookie');
  }

  /**
   * Returns the session id if there is an active session.
   */
  public function get_session_id() {
    if (!isset($this->session_id)) {
      if (isset($_COOKIE[$this->session_cookie_name()])) {
        $this->session_id = $_COOKIE[$this->session_cookie_name()];
      }
    }
    return $this->session_id;
  }

  /**
   * Generates a new session id.
   */
  public function generate_session_id() {
    $this->session_id = Crypt::randomBytesBase64();
    return $this->session_id;
  }

  /**
   * Sets a cookie for the active user session.
   */
  public function send_session_cookie() {
    $session_id = $this->get_session_id();
    if (!$session_id) {
      throw new \Exception('Session ID is expected.');
    }
    $expire = \Drupal::time()->getRequestTime() + (5 * 86400);
    setcookie($this->session_cookie_name(), $session_id, $expire, '/');
  }

  /**
   * Returns the cookie name for the custom user session.
   */
  public function session_cookie_name() {
    return 'wv_child_state';
  }

  /**
   * Starts a new session.
   */
  public function session_start() {
    $this->generate_session_id();
    $this->send_session_cookie();
    $this->cached_data = [];
  }

  /**
   * Returns the temp store key from session data key.
   */
  public function getRealKey($key) {
    $session_id = $this->get_session_id();
    if (!$session_id) {
      throw new \Exception('Session ID is expected.');
    }
    return $key . '.' . Crypt::hashBase64($session_id);
  }

  /**
   * Returns the session value for a given key.
   */
  public function getData($key, $default = NULL) {
    $session_id = $this->get_session_id();
    if ($session_id) {
      if (!isset($this->cached_data[$key])) {
        $realKey = $this->getRealKey($key);
        $this->cached_data[$key] = $this->keyValueStore->get($realKey, $default);
      }
      return $this->cached_data[$key];
    }
    return $default;
  }

  /**
   * Saves a value for a given key.
   */
  public function setData($key, $value) {
    $session_id = $this->get_session_id();
    if (!$session_id) {
      $this->session_start();
    }
    $this->cached_data[$key] = $value;
    $realKey = $this->getRealKey($key);
    $this->keyValueStore->set($realKey, $value);
  }

  /**
   * Deletes an item from session store.
   */
  public function deleteData($key) {
    $session_id = $this->get_session_id();
    if ($session_id) {
      unset($this->cached_data[$key]);
      $realKey = $this->getRealKey($key);
      $this->keyValueStore->delete($realKey);
    }
  }

}
