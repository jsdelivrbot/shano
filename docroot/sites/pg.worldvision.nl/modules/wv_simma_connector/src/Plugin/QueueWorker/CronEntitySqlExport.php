<?php

/**
 * @file
 * Queue worker.
 */

namespace Drupal\wv_simma_connector\Plugin\QueueWorker;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Queue\QueueWorkerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\wv_simma_connector\Services\EntitySqlExport;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
* Exports forms & other entities on CRON run.
*
* @QueueWorker(
*   id = "wv_entity_sql_export",
*   title = @Translation("Cron Form SQL Export"),
*   cron = {"time" = 10}
* )
*/
class CronEntitySqlExport extends QueueWorkerBase implements ContainerFactoryPluginInterface {
  // Allow class to use t() translation callback.
  use StringTranslationTrait;

  /**
   * The node storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $nodeStorage;

  /**
   * Constructor.
   */
  public function __construct(EntitySqlExport $entity_sql_export) {
    $this->entity_sql_export = $entity_sql_export;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      \Drupal::service('wv_simma_connector.entity_sql_export')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function processItem($data) {
    // Log errors, so user can see what happens beyond the scene.
    try {
      // Using the storage controller.
      if (!$entity = \Drupal::entityTypeManager()->getStorage($data->entity_type)->load($data->entity_id)) {
        throw new \Exception(
          $this->t('Entity !id with type !type can not be loaded and exported.',
            ['!id' => $data->entity_id, '!type' => $data->entity_type]
          )
        );
      }

      // Check whether table exists & has all needed columns.
      if (!$this->entity_sql_export->tableExists($data->table_name)) {
        $this->entity_sql_export->createTable(
          $data->table_name,
          $this->entity_sql_export->getTableSchema($entity, $data->table_name)
        );
      }
      else {
        $table_schema = $this->entity_sql_export->getTableSchema($entity, $data->table_name);

        $fields_schema = !empty($table_schema['fields']) ? $table_schema['fields'] : [];

        $external_schemas = \Drupal::service('config.factory')->getEditable('wv_simma_connector.external_schemas');
        $external_schema = $external_schemas->get($data->table_name);

        if (!empty($external_schema['fields'])) {
          $old_fields_schema = $external_schema['fields'];

          // Add missing columns.
          foreach (array_diff_key($fields_schema, $old_fields_schema) as $field_name => $field_schema) {
            // Create all new added fields.
            $this->entity_sql_export->addField($data->table_name, $field_name, $field_schema);

            $schema_changed = TRUE;
          }

          // Drop deleted columns.
          foreach (array_diff_key($old_fields_schema, $fields_schema) as $field_name => $field_schema) {
            // Create all new added fields.
            $this->entity_sql_export->dropField($data->table_name, $field_name);

            $schema_changed = TRUE;
          }

          // Cache new schema to avoid double field adding attempts.
          if (!empty($schema_changed)) {
            $external_schema['fields'] = $fields_schema;

            // Store fields schema to compare later to find any fields differences.
            $external_schemas->set($data->table_name, $external_schema)->save();
          }
        }

        // If somebody reinstalled module & dropped configs put whole
        // config again to avoid losing new added/deleted columns data.
        if (!$external_schema) {
          $external_schemas->set($data->table_name, $table_schema)->save();
        }
      }

      // Do data export.
      if ($fields_mapping = $this->entity_sql_export->fields_mapping[$data->table_name]) {
        $values = [];

        foreach ($fields_mapping as $field_name => $columns_mapping) {
          // Use some dummy fields to export custom data regarding spec, e.g. project ID.
          if (preg_match('/^(.*)_dummy$/', $field_name, $matches)) {

            foreach ($columns_mapping as $source_col => $destination_cols) {

              foreach ((array) $destination_cols as $destination_col) {
                switch ($destination_col) {
                  case 'child_project_id':
                  case 'child_sequence':
                    list($child_project_id, $child_sequence) = explode('-', $entity->{$matches[1]}->$source_col);
                    $values[$destination_col] = $$destination_col;
                    break;

                  default:
                    $values[$destination_col] = $entity->{$field_name}->$source_col;
                    break;
                }
              }
            }

            continue;
          }

          foreach ($columns_mapping as $source_col => $destination_cols) {
            foreach ((array) $destination_cols as $destination_col) {
              $values[$destination_col] = $entity->{$field_name}->$source_col;
            }
          }
        }

        // Insert fields values.
        if ($values) {
          $this->entity_sql_export->insert($data->table_name, $values);
        }
      }
    } catch (\Exception $e) {
      \Drupal::logger('simma_connector.export')->error($e->getMessage());

      // Throw error to not loss queue data if error happened.
      throw  $e;

    } catch (\Error $e) {
      \Drupal::logger('simma_connector.export')->error($e->getMessage());

      // Throw error to not loss queue data if error happened.
      throw  $e;
    }
  }
}
