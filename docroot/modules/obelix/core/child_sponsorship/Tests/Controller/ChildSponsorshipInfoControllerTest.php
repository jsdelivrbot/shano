<?php

namespace Drupal\child_sponsorship\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Provides automated tests for the child_sponsorship module.
 */
class ChildSponsorshipInfoControllerTest extends WebTestBase {
  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return array(
      'name' => "child_sponsorship ChildSponsorshipInfoController's controller functionality",
      'description' => 'Test Unit for module child_sponsorship and controller ChildSponsorshipInfoController.',
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
   * Tests child_sponsorship functionality.
   */
  public function testChildSponsorshipInfoController() {
    // Check that the basic functions of module child_sponsorship.
    $this->assertEquals(TRUE, TRUE, 'Test Unit Generated via App Console.');
  }

}
