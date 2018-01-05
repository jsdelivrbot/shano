<?php

namespace Drupal\obelix_contacts\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a links to social media pages of our company.
 *
 * @Block(
 *   id = "social_links_block",
 *   admin_label = @Translation("Social links block"),
 *   category = @Translation("World Vision"),
 * )
 */
class SocialLinksBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = \Drupal::config('obelix_contacts.settings');
    return array(
      '#theme' => 'obelix_contacts_social_links_block',
      '#ourFacebookPage' => $config->get('ourFacebookPage'),
      '#ourTwitterPage' => $config->get('ourTwitterPage'),
      '#ourGooglePlusPage' => $config->get('ourGooglePlusPage'),
      '#ourXingPage' => $config->get('ourXingPage'),
    );
  }

}
