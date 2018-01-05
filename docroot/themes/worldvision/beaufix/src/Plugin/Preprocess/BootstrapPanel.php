<?php
/**
 * @file
 * Contains \Drupal\beaufix\Plugin\Preprocess\BootstrapPanel.
 */

namespace Drupal\beaufix\Plugin\Preprocess;

use Drupal\bootstrap\Annotation\BootstrapPreprocess;
use Drupal\bootstrap\Plugin\Preprocess\BootstrapPanel as BootstrapPanelBase;
use Drupal\bootstrap\Utility\Element;
use Drupal\bootstrap\Utility\Variables;

/**
 * Pre-processes variables for the "bootstrap_panel" theme hook.
 *
 * @ingroup plugins_preprocess
 *
 * @BootstrapPreprocess("bootstrap_panel")
 */
class BootstrapPanel extends BootstrapPanelBase
{

  /**
   * {@inheritdoc}
   */
  protected function preprocessElement(Element $element, Variables $variables)
  {

    parent::preprocessElement($element, $variables);

    $type = $element->getProperty('type');
    if ($type == 'radios') {

    }

  }

}
