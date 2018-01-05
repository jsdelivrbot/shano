<?php

namespace Drupal\child_manager\Tests;

use Drupal\simpletest\WebTestBase;
use Drupal\datasourcehandler\DatasourceServiceLocal;

/**
 * Provides automated tests for the child_manager module.
 */
class ChildManagerTest extends WebTestBase {

  /**
   * Drupal\datasourcehandler\DatasourceServiceLocal definition.
   *
   * @var Drupal\datasourcehandler\DatasourceServiceLocal
   */
  protected $datasource;
  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return array(
      'name' => "child_manager ChildManager's controller functionality",
      'description' => 'Test Unit for module child_manager and controller ChildManager.',
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
   * Tests child_manager functionality.
   */
  public function testChildManager() {
    // Check that the basic functions of module child_manager.
    $this->assertEquals(TRUE, TRUE, 'Test Unit Generated via App Console.');
  }

}
