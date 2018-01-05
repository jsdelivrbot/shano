<?php

namespace Drupal\obelix_contacts\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'StatisticsFooterBlock' Block.
 *
 * @Block(
 *   id = "accounting_footer_block",
 *   admin_label = @Translation("Footer accounting block"),
 *   category = @Translation("World Vision"),
 * )
 */
class AccountingFooterBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = \Drupal::config('obelix_contacts.settings');
    return array(
      '#theme' => 'obelix_contacts_accounting_footer_block',
      '#generalDonationAccount' => $config->get('generalDonationAccount.value'),
      '#creditorIdNumber' => $config->get('creditorIdNumber'),
    );
  }

}
