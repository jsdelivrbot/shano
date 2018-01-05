<?php

namespace Drupal\obelix_contacts\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Contacts' Block.
 *
 * @Block(
 *   id = "contacts_block",
 *   admin_label = @Translation("Contacts block"),
 *   category = @Translation("World Vision"),
 * )
 */
class ContactsBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = \Drupal::config('obelix_contacts.settings');
    return array(
      '#theme' => 'obelix_contacts_contact_block',
      '#global_phone' => $config->get('globalPhone'),
      '#open_hours' => $config->get('openingHours'),
      '#contact_mail' => $config->get('globalEmail'),
    );
  }

}
