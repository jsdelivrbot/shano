<?php
/**
 * User: MoeVoe
 * Date: 25.05.16
 * Time: 23:17
 */

namespace Drupal\ivision_nav16\iVisionController;


class IVisionConnect {
  /**
   * @var array
   *  The base uri to Set up a Connection to the World Vision Webservice.
   */
  private $uri;

  /**
   * @var array
   *  The company name to Set up a Connection to the World Vision Webservice.
   */
  private $companyName;

  /**
   * @var array
   *  The user to Set up a Connection to the World Vision Webservice.
   */
  private $user;

  /**
   * @var array
   *  The password to Set up a Connection to the World Vision Webservice.
   */
  private $password;

  /**
   * IVisionBase constructor.
   * @param null $uri
   * @param null $companyName
   * @param null $user
   * @param null $password
   */
  public function __construct($uri = NULL, $companyName = NULL, $user = NULL, $password = NULL) {
    if ($uri !== NULL && $companyName !== NULL && $user !== NULL && $password !== NULL) {
      $this->setConnection($uri, $companyName, $user, $password);
    }
  }

  public function setConnection($uri, $companyName, $user, $password) {
    if (!isset($uri)) {
      throw new IVisionException('API URI is missing');
    }
    if (!isset($companyName)) {
      throw new IVisionException('API company name is missing');
    }
    if (!isset($user)) {
      throw new IVisionException('API user ist missing');
    }
    if (!isset($password)) {
      throw new IVisionException('API password ist missing');
    }
    $this->uri = $uri;
    $this->companyName = $companyName;
    $this->user = $user;
    $this->password = $password;
  }

  /**
   * @return string
   */
  public function getUri() {
    return $this->uri;
  }

  /**
   * @return string
   */
  public function getCompanyName() {
    return $this->companyName;
  }

  /**
   * @return string
   */
  public function getUser() {
    return $this->user;
  }

  /**
   * @return string
   */
  public function getPassword() {
    return $this->password;
  }


}
