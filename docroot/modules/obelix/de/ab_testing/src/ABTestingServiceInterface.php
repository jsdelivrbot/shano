<?php

namespace Drupal\ab_Testing;

/**
 * Interface ABTestingServiceInterface.
 *
 * @package Drupal\ab_testing
 */
interface ABTestingServiceInterface{

  /**
   * Overwrites the given $data if the user is selected for
   * alternative test case.
   * Sets the user a/b testing group if not already set.
   *
   * @param integer $id
   *  The a/b testing id.
   * @param mixed $data
   *  The data which might be overwritten.
   * @param string $default
   *  the default test case.
   */
  public function set($id, &$data = NULL, $default = 'a');

  /**
   * Returnes the user group from the session.
   * If no user group is set it returns NULL.
   *
   * @return string
   *  User group or NULL
   */
  public function getUserGroup();

  /**
   * Set user group for a/b testing in session.
   * If user have already a user group the old group will be used.
   *
   * @param $rule_id
   *  The rule set key.
   * @param mixed $overwrite
   *  Force a user group. Will be set if the user already have a group.
   * @return string
   *  User group.
   */
  public function setUserGroup($rule_id, $overwrite = NULL);

  /**
   * Get the different selection rules.
   *
   * You can define a new rule by adding a new array entry.
   * a, b ,c define the test cases.
   * The value of the test case for example: [1, 50]
   * defines the percentage of the test case.
   *
   * @param $rule_id
   *  The rule set key.
   * @return array
   *  The selection rule
   */
  public function getSelectionRule($rule_id);

}
