<?php

namespace Drupal\project_manager\Tests;

use Drupal\simpletest\WebTestBase;
use Drupal\datasourcehandler\DatasourceServiceLocal;

/**
 * Provides automated tests for the project_manager module.
 */
class ProjectManagerTest extends WebTestBase {

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
      'name' => "project_manager ProjectManager's controller functionality",
      'description' => 'Test Unit for module project_manager and controller ProjectManager.',
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
   * Tests project_manager functionality.
   */
  public function testProjectManager() {
    // Check that the basic functions of module project_manager.
    $this->assertEquals(TRUE, TRUE, 'Test Unit Generated via App Console.');
  }

}
