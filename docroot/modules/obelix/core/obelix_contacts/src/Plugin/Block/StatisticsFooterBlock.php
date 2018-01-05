<?php

namespace Drupal\obelix_contacts\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'StatisticsFooterBlock' Block.
 *
 * @Block(
 *   id = "statistics_footer_block",
 *   admin_label = @Translation("Footer statistics block"),
 *   category = @Translation("World Vision"),
 * )
 */
class StatisticsFooterBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return array(
      '#theme' => 'obelix_contacts_statistics_footer_block',
    );
  }

}
