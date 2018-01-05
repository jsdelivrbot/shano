<?php

namespace Drupal\obelix_contacts\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'SubscribeFormBlock' Block.
 *
 * @Block(
 *   id = "subscribe_form_block",
 *   admin_label = @Translation("Subscribe form block"),
 *   category = @Translation("World Vision"),
 * )
 */
class SubscribeFormBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return array(
      '#theme' => 'obelix_contacts_subscribe_block',
    );
  }

}
