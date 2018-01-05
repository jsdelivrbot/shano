<?php

/**
 * @file Contains the code to generate the custom drush commands.
 */

use Drupal\Core\Database\Database;

/**
 * Implements hook_drush_command().
 */
function wv_tools_drush_command() {
  $items = array();

  $items['delete-content'] = [
    'description' => 'Deletes all entities of a given entity type',
    'arguments' => [
      'entity-type' => 'The entity type of deleting content.',
    ],
    'drupal dependencies' => ['wv_tools'],
    'aliases' => ['wv:dc'],
  ];

  $items['delete-fields-content'] = [
    'description' => 'Deletes all entities fields content of a given entity type & bundle',
    'arguments' => [
      'entity-type' => 'The entity type of deleting fields content.',
      'bundle' => 'The entity bundle of deleting fields content.',
    ],
    'drupal dependencies' => ['wv_tools'],
    'aliases' => ['wv:dfc'],
  ];

  $items['import-config-file'] = [
    'description' => 'Import explicit config path to active config. Please use absolute path, it does not work with relative ones.',
    'arguments' => [
      'file-path' => 'Path to the config which will be imported',
    ],
    'drupal dependencies' => ['wv_tools'],
    'aliases' => ['wv:icf'],
  ];

  return $items;
}

/**
 * Drush call back function, deletes all entities of a given type.
 *
 * @param string $entity_type
 */
function drush_wv_tools_delete_content($entity_type = '') {
  $batch = array(
    'title' => t('Deleting content of entity type !type', ['!type' => $entity_type]),
    'operations' => array(
      array('wv_tools_content_delete_operation', array($entity_type)),
    ),
    'finished' => 'wv_tools_content_delete_finished',
  );

  batch_set($batch);
  drush_backend_batch_process();
}

/**
 * Drush call back function, deletes all entities fields content of a given type & bundle.
 *
 * @param string $entity_type
 * @param string $bundle
 */
function drush_wv_tools_delete_fields_content($entity_type = '', $bundle = NULL) {
  if (!$bundle) {
    $bundle = $entity_type;
  }

  $properties = array(
    'entity_type' => $entity_type,
    'bundle' => $bundle,
    'include_deleted' => TRUE,
  );

  $entity_manager = \Drupal::entityTypeManager();

  $fields = $entity_manager->getStorage('field_config')->loadByProperties($properties);
  $entity_storage = $entity_manager->getStorage($entity_type);
  $entity_definition = $entity_manager->getDefinition($entity_type);
  $revisionable = $entity_definition->isRevisionable();

  $info = $entity_manager->getDefinitions();

  $count = 0;

  drush_print(sprintf('Starting cleanup of %s bundle data of %s entity type', $bundle, $entity_type) . PHP_EOL);
  
  foreach ($fields as $field_definition) {
    // Proceed only if the module implementing the entity has not been uninstalled already.
    if (isset($info[$entity_type])) {
      $storage_definition = $field_definition->getFieldStorageDefinition();
      $is_deleted = $storage_definition->isDeleted();
      $table_mapping = $entity_storage->getTableMapping();
      $table_name = $table_mapping->getDedicatedDataTableName($storage_definition, $is_deleted);

      $db = Database::getConnection();

      try {
        drush_print(sprintf('Deleting %s bundle data from %s table...', $bundle, $table_name));

        $db->delete($table_name)
          ->condition('bundle', $bundle)
          ->execute();

        $count++;

      } catch (\Exception $e) {
        drush_print($e->getMessage());
      }

      if ($revisionable) {
        try {
          if ($revision_name = $table_mapping->getDedicatedRevisionTableName($storage_definition, $is_deleted)) {
            drush_print(sprintf('Deleting %s bundle data from %s table...', $bundle, $revision_name));

            $db->delete($revision_name)
              ->condition('bundle', $bundle)
              ->execute();

            $count++;
          }

        } catch (\Exception $e) {
          drush_print($e->getMessage());
        }
      }
    }
  }

  drush_print(PHP_EOL . sprintf('Done %s tables were cleaned from %s bundle data of %s entity type', $count, $bundle, $entity_type));
}

/**
 * Drush call back function, imports config file.
 *
 * @param string $file_path
 */
function drush_wv_tools_import_config_file($file_path) {
  $source = new \Drupal\Core\Config\FileStorage(dirname($file_path));

  $name = preg_replace('/\.yml/', '', trim(basename($file_path)));

  if (!$value = $source->read($name)) {
    switch (TRUE) {
      case !$data = file_get_contents($source->getFilePath($name)):
      case !$value = $source->decode($data):
        return;
    }
  }

  /** @var \Drupal\Core\Config\StorageInterface $active_storage */
  \Drupal::service('config.storage')->write($name, $value);
}

/**
 * Batch operation callback.
 */
function wv_tools_content_delete_operation($entity_type, &$context) {
  $context['finished'] = 0;

  // Init context.
  if (empty($context['sandbox'])) {
    $context['sandbox']['progress'] = 0;

    // Query all entities you want for example taxonomy term from tags vocabulary
    $query = \Drupal::entityQuery($entity_type);
    $ids = $query->execute();

    $total = count($ids);

    if (empty($total) || !is_numeric($total)) {
      throw new Exception('Total rows number is empty');
    }

    $context['sandbox']['max'] = $total;
    $context['sandbox']['ids'] = $ids;

    $limit = Drupal::service('config.factory')
      ->getEditable('wv_tools.settings')
      ->get('content_delete_items_limit');

    if (!$limit) {
      $limit = 50;
    }

    if ($total < $limit) {
      $limit = $total;
    }

    $context['sandbox']['limit'] = $limit;

    return;
  }

  $limit = $context['sandbox']['limit'];

  if ($limit + $context['sandbox']['progress'] > $context['sandbox']['max']) {
    $limit = $context['sandbox']['max'] - $context['sandbox']['progress'];
  }

  // Delete content.
  $storage_handler = \Drupal::entityTypeManager()->getStorage($entity_type);

  $entities = $storage_handler->loadMultiple(array_splice($context['sandbox']['ids'], 0, $limit));
  $storage_handler->delete($entities);


  $context['sandbox']['progress'] += $limit;

  $context['results'][] = 'entities were deleted';

  $context['message'] = $limit . ' entities were deleted, total : ' . $context['sandbox']['progress'] . ' of '
    . $context['sandbox']['max'];

  if ($context['sandbox']['progress'] >= $context['sandbox']['max']) {
    $context['finished'] = 1;
  }
}

/**
 * Batch finished.
 */
function wv_tools_content_delete_finished($success, $results, $operations) {
  if ($success) {
    $message = count($results) . ' entities processed.';
  }
  else {
    $message = t('Finished with an error.');
  }

  drush_print_r($message);
}
