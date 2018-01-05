<?php

/**
 * @files
 * Contains \Drupal\editorial\EditorialFieldGlobalConfigurationManager
 */

namespace Drupal\editorial;

use Drupal\layout_plugin\Layout;

/**
 * Class EditorialFieldGlobalConfigurationManager
 * @package Drupal\editorial
 */
abstract class EditorialFieldGlobalConfigurationManager {

  /**
   * Get all layouts definitions.
   *
   * @param bool $only_available
   *  TRUE if you only want to receive available definitions.
   * @return array
   *  An multidimensional array of layout definitions keyed by the plugin id.
   */
  public static function getLayoutDefinitions($only_available = FALSE) {
    $definitions = Layout::layoutPluginManager()->getDefinitions();

    if ($only_available) {
      if ($available = \Drupal::configFactory()
        ->get('editorial.editorial_field.global_configuration')
        ->get('layouts')) {
        $definitions = array_intersect_key($definitions, $available);
      }
    }
    return $definitions;
  }

  /**
   * Get all layouts as an options array.
   *
   * @param bool $only_available
   *  TRUE if you only want to receive available options.
   * @return array
   *  An multidimensional array of layout options keyed by the plugin id.
   */
  public static function getLayoutOptions($only_available = FALSE) {
    $options = Layout::layoutPluginManager()->getLayoutOptions();

    if ($only_available) {
      if ($available = \Drupal::configFactory()
        ->get('editorial.editorial_field.global_configuration')
        ->get('layouts')) {
        $options = array_intersect_key($options, $available);
      }
    }

    return $options;
  }

  public static function getEntityOptions($only_available = FALSE) {
    $options = [];

    $entities = \Drupal::entityTypeManager()->getDefinitions();
    foreach ($entities as $entity_type_id => $entity_info) {
      if ($entity_info->getGroup() == 'content') {
        $options[$entity_type_id] = $entity_info->getLabel();
      }
    }

    return $options;
  }

  public static function getEntityBundleOptions($entity_type_id) {
    $options = [];

    $bundles = \Drupal::getContainer()->get('entity_type.bundle.info')->getBundleInfo($entity_type_id);
    foreach ($bundles as $bundle_id => $bundle_info) {
      $options[$bundle_id] = $bundle_info['label'];
    }

    return $options;
  }
}
