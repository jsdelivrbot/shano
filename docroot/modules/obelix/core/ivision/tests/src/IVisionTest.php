<?php
/**
 * @file
 * Contains \Drupal\Tests\ivision\Unit\IVisionTest.
 */

namespace Drupal\ivision\Unit;

use Drupal\simpletest\WebTestBase;

/**
 * @coversDefaultClass \Drupal\forms_suite\MailHandler
 * @group forms
 */
class IVisionTest extends WebTestBase {

  /**
   * Perform any initial set up tasks that run before every test method
   */
  public function setUp() {
    parent::setUp();
    $this->user = $this->drupalCreateUser(array('access content'));
  }

}
