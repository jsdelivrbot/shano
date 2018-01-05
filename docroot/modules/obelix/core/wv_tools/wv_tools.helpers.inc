<?php

/**
 * @file
 * Often used functions & other helpers.
 */

/**
 * Checks whether entity type has field.
 *
 * It differs from entity::hasField function due that you don't need to have an entity object & can e.g. use
 * this to search entities globally on multi site where different countries have different set of fields.
 *
 * @param $entity_type
 * @param $field_name
 * @param null $bundle
 *
 * @return bool
 */
function wv_entity_type_has_field($entity_type, $field_name, $bundle = NULL) {
  if (!$bundle) {
    $bundle = $entity_type;
  }

  if ($fields_definitions = \Drupal::service('entity_field.manager')->getFieldDefinitions($entity_type, $bundle)) {
    return !empty($fields_definitions[$field_name]);
  }

  return FALSE;
}
