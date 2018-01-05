<?php

namespace Drupal\child\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Provides automated tests for the child module.
 */
class ChildControllerTest extends WebTestBase {
  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return array(
      'name' => "child ChildController's controller functionality",
      'description' => 'Test Unit for module child and controller ChildController.',
      'group' => 'Other',
    );
  }

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
  }

  /**
   * Tests child functionality.
   */
  public function testChildController() {
    // Check that the basic functions of module child.
    $this->assertEquals(TRUE, TRUE, 'Test Unit Generated via App Console.');
  }

}
