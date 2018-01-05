<?php

namespace Drupal\country_manager\Tests;

use Drupal\simpletest\WebTestBase;
use Drupal\datasourcehandler\DatasourceServiceLocal;

/**
 * Provides automated tests for the country_manager module.
 */
class CountryManagerTest extends WebTestBase {

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
      'name' => "country_manager CountryManager's controller functionality",
      'description' => 'Test Unit for module country_manager and controller CountryManager.',
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
   * Tests country_manager functionality.
   */
  public function testCountryManager() {
    // Check that the basic functions of module country_manager.
    $this->assertEquals(TRUE, TRUE, 'Test Unit Generated via App Console.');
  }

}
