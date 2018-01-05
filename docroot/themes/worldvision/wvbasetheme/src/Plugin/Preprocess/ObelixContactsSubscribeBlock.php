<?php
/**
 * @file
 * Contains \Drupal\wvbasetheme\Plugin\Preprocess\NodeGiftType.
 */

namespace Drupal\wvbasetheme\Plugin\Preprocess;

use Drupal\bootstrap\Plugin\Preprocess\PreprocessBase;
use Drupal\Core\Url;
use Drupal\Core\Link;

/**
 * Pre-processes variables for the "node__gift_type" theme hook.
 *
 * @ingroup plugins_preprocess
 *
 * @BootstrapPreprocess("obelix_contacts_subscribe_block")
 */
class ObelixContactsSubscribeBlock extends PreprocessBase
{

  /**
   * {@inheritdoc}
   */
  public function preprocess(array &$variables, $hook, array $info) {
    $variables['newsletter_form_url'] = \Drupal::config('wv_site.settings.routing')
      ->get('newsletter_form_url');
  }
}
