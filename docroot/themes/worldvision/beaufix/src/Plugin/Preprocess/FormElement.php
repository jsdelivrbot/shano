<?php
/**
 * @file
 * Contains \Drupal\beaufix\Plugin\Preprocess\FormElement.
 */

namespace Drupal\beaufix\Plugin\Preprocess;

use Drupal\bootstrap\Annotation\BootstrapPreprocess;
use Drupal\bootstrap\Plugin\Preprocess\FormElement as FormElementBootstrap;
use Drupal\bootstrap\Utility\Element;
use Drupal\bootstrap\Utility\Variables;

/**
 * Pre-processes variables for the "form_element" theme hook.
 *
 * @ingroup plugins_preprocess
 *
 * @BootstrapPreprocess("form_element")
 */
class FormElement extends FormElementBootstrap
{

  /**
   * {@inheritdoc}
   */
  public function preprocessElement(Element $element, Variables $variables)
  {
    parent::preprocessElement($element, $variables);

    $type = $element->getProperty('type');
    if ($type == 'textarea' || $type == 'radio' || $type == 'checkbox') {
      $variables['is_form_group'] = TRUE;
    }
    if ($type == 'number') {
      $variables->removeClass('form-inline');
    }
  }
}
