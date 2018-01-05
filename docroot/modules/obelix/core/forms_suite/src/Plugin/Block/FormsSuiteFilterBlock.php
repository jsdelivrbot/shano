<?php
/**
 * @file
 * Contains \Drupal\forms_suite\Plugin\Block\FormsSuiteFilterBlock.
 */
namespace Drupal\forms_suite\Plugin\Block;
use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'forms_suite_filters' block.
 *
 * @Block(
 *   id = "forms_suite_filters",
 *   admin_label = @Translation("Forms Suite Filters"),
 *   category = @Translation("Forms Suite")
 * )
 */

class FormsSuiteFilterBlock extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function build() {
    return \Drupal::formBuilder()->getForm('Drupal\forms_suite\Form\FormFilterForm');
  }
}
