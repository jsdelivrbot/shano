<?php
/**
 * @file
 * Contains \Drupal\beaufix\Plugin\Alter\ThemeSuggestions.
 */

namespace Drupal\beaufix\Plugin\Alter;

use Drupal\bootstrap\Annotation\BootstrapAlter;
use Drupal\bootstrap\Plugin\Alter\ThemeSuggestions as ThemeSuggestionsBase;

/**
 * Implements hook_theme_suggestions_alter().
 *
 * @ingroup plugins_alter
 *
 * @BootstrapAlter("theme_suggestions")
 */
class ThemeSuggestions extends ThemeSuggestionsBase
{

  /**
   * {@inheritdoc}
   */
  public function alter(&$suggestions, &$variables = NULL, &$hook = NULL)
  {

    parent::alter($suggestions, $variables, $hook);

    if ($hook == 'fieldset') {
//      if (isset($variables['element']['#type']) && $variables['element']['#type'] == 'radios') {
//        unset($suggestions);
//        $suggestions[] = 'input__form_control';
//        unset($variables['element']['#theme_wrappers'][1]);
//        unset($variables['element']['attributes']['class']);
//      }
    }


  }

}
