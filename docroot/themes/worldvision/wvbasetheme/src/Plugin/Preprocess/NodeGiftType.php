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
 * @BootstrapPreprocess("node__gift_type")
 */
class NodeGiftType extends PreprocessBase
{

  /**
   * {@inheritdoc}
   */
  public function preprocess(array &$variables, $hook, array $info) {
    $all_gifts_url = \Drupal::config('wv_site.settings.routing')
      ->get('gift_type_all_gifts_url');

    $variables['all_gifts_link'] = Link::fromTextAndUrl(
      t('See all gifts'),
      Url::fromUri("internal:$all_gifts_url")->setOptions([
        'attributes' => array('class' => ['btn-beauty', 'btn-fullsize-xs']),
      ])
    );
  }
}
