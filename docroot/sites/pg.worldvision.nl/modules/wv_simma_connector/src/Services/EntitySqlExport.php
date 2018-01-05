<?php

namespace Drupal\wv_simma_connector\Services;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Database\Database;
use Drupal\Core\Queue\QueueFactory;
use Drupal\Core\Queue\QueueInterface;

/**
 * Provides form sql export service.
 */
class EntitySqlExport {

  // Allow class to use t() translation callback.
  use StringTranslationTrait;

  // Used to return previously active database connection back.
  protected $previously_active_db;

  // Restrict exporting context to these fields & types.
  public $allowed_properties;

  // This field is used to determine whether or not the form should be exported.
  public $excluded_fields;

  // All these field types can be exported to external database.
  public $allowed_field_types;

  /**
   * When logic prepares fields schema it can change it's columns names in case
   * if field has multiple columns, so we need to know all allowed real fields
   * machine names during export to fetch this data from exporting entity object.
   */
  public $fields_mapping = [];

  /**
   * Init field configs which can be set in GUI & during module installation.
   *
   * EntitySqlExport constructor.
   */
  public function __construct() {
    $export_settings = \Drupal::service('config.factory')
      ->getEditable('wv_simma_connector.settings')
      ->get('export');

    $settings_keys = ['allowed_properties', 'allowed_field_types', 'excluded_fields'];

    foreach ($settings_keys as $key) {
      $this->{$key} = array_map(
        function ($v) {
          return trim($v);
        },
        explode(',', $export_settings[$key])
      );
    }
  }

  /**
   * Queues entity for further export.
   *
   * @param $entity
   * @param $table_name
   */
  public function queue($entity, $table_name) {
    /** @var QueueFactory $queue_factory */
    $queue_factory = \Drupal::service('queue');

    /** @var QueueInterface $queue */
    $queue = $queue_factory->get('wv_entity_sql_export');

    $item = new \stdClass();

    $item->table_name = $table_name;
    $item->entity_id = $entity->id();
    $item->entity_type = $entity->getEntityTypeId();

    $queue->createItem($item);
  }

  /**
   * Changes active db connection to previously active db.
   */
  public function set_origin_db() {
    if (!empty($this->previously_active_db)) {
      Database::setActiveConnection($this->previously_active_db);
    } else {
      // If we have no previously active db use default config.
      Database::setActiveConnection();
    }
  }

  /**
   * Changes active db connection to export db.
   */
  public function set_export_db() {
    $this->previously_active_db = Database::setActiveConnection('export');
  }

  /**
   * Generates table schema.
   */
  public function getTableSchema($entity, $table_name) {
    return [
      'description' => $this->t(
        '@form_id table schema, it describes all form allowed fields & properties',
        ['@form_id' => $table_name]
      ),
      'fields' => $this->getFieldsSchema($table_name, $entity->getFieldDefinitions()),
      'primary key' => ['id'],
      'indexes' => ['entity_id' => ['id']],
    ];
  }

  /**
   * Check whether table was created.
   */
  public function tableExists($table_name) {
    $this->set_export_db();

    $schema = Database::getConnection()->schema();
    $table_exists = $schema->tableExists($table_name);

    $this->set_origin_db();

    return $table_exists;
  }

  /**
   * Creates table.
   */
  public function createTable($table_name, $table_schema) {
    $this->set_export_db();

    $schema = Database::getConnection()->schema();

    if (!$schema->tableExists($table_name)) {
      $schema->createTable($table_name, $table_schema);
    }

    $this->set_origin_db();

    $config = \Drupal::service('config.factory')->getEditable('wv_simma_connector.external_schemas');

    // Store fields schema to compare later to find any fields differences.
    $config->set($table_name, $table_schema)->save();
  }

  /**
   * Drops table.
   */
  public function dropTable($table_name) {
    $this->set_export_db();

    $schema = Database::getConnection()->schema();
    $schema->dropTable($table_name);

    $this->set_origin_db();

    \Drupal::service('config.factory')->getEditable("wv_simma_connector.external_schemas.$table_name")->delete();
  }

  /**
   * Generates fields definitions according being exported entity.
   */
  public function getFieldsSchema($table_name, array $fields_definitions = []) {
    $fields_schema = &drupal_static(__CLASS__ . '::' . __FUNCTION__, []);

    if (!isset($fields_schema[$table_name])) {
      $fields_schema[$table_name] = [];

      $this->fields_mapping[$table_name] = [];

      // Check all fields, select only allowed types, custom fields & entity properties & generate SQL schema.
      foreach ($fields_definitions as $field_definition) {

        $field_name = $field_definition->getName();
        $field_type = $field_definition->getType();

        // Check if we have child ref fields & add project id custom property to schema for export.
        if ($field_type == 'wovi_child_field') {
          $fields_schema[$table_name]['child_project_id'] = [
            'type' => 'varchar_ascii',
            'length' => 60,
          ];

          $fields_schema[$table_name]['child_sequence'] = [
            'type' => 'varchar_ascii',
            'length' => 60,
          ];

          // Store relation between real & exporting column & field names.
          $this->fields_mapping[$table_name][$field_name . '_dummy']['childSequenceNo'][] = 'child_project_id';
          $this->fields_mapping[$table_name][$field_name . '_dummy']['childSequenceNo'][] = 'child_sequence';
        }

        // Check whether field is allowed.
        if (in_array($field_type, $this->allowed_field_types) && !in_array($field_name, $this->excluded_fields)
          // Allow all fields with field_prefix (in case if it has appropriate type).
          && (in_array($field_name, $this->allowed_properties) || preg_match('/^field_/', $field_name))) {

          $schema = $field_definition->getFieldStorageDefinition()->getSchema();

          // Store real machine names before export.
          $this->fields_mapping[$table_name][$field_name] = [];

          // If field has no multiple columns avoid suffix.
          if (count($schema['columns']) == 1) {

            $fields_schema[$table_name][$field_name] = current($schema['columns']);

            // By default ID field is nullable (can be assigned to NULL). However
            // we use id as primary key by sqlsrv driver doesn't let create it as
            // primary key coz of it's nullable property, so we change it to not null.
            if ($field_name == 'id') {
              $fields_schema[$table_name][$field_name]['not null'] = TRUE;
            }

            // Store relation between real & exporting column & field names.
            $this->fields_mapping[$table_name][$field_name][key($schema['columns'])] = $field_name;
          }
          else {

            foreach ($schema['columns'] as $column => $column_schema) {
              $destination_column = "{$field_name}_{$column}";

              // Allow user to exclude particular columns of multiple columns fields.
              if (!in_array($destination_column, $this->excluded_fields)) {
                $fields_schema[$table_name][$destination_column] = $column_schema;
                // Store relation between real & exporting column & field names.
                $this->fields_mapping[$table_name][$field_name][$column] = $destination_column;
              }
            }
          }
        }

        // Check if we have giftshop field alter it's blob column to let FreeTDS driver work.
        if ($field_type == 'wovi_giftshop_field' && isset($fields_schema[$table_name]["{$field_name}_gifts"])) {
          $fields_schema[$table_name]["{$field_name}_gifts"] = [
            'type' => 'text',
            'size' => 'big',
          ];
        }
      }
    }

    return $fields_schema[$table_name];
  }

  /**
   * Insert data into external db.
   */
  public function insert($table_name, $values) {
    $this->set_export_db();

    $query = Database::getConnection()->insert($table_name);

    if (!$query->fields($values)->execute()) {
      $this->set_origin_db();

      throw new \Exception($this->t('Error during attempt to insert data into @table', ['@table' => $table_name]));
    }

    $this->set_origin_db();

  }

  /**
   * Adds new column to the specified table.
   */
  public function addField($table_name, $field_name, $field_schema) {
    $this->set_export_db();

    Database::getConnection()->schema()->addField($table_name, $field_name, $field_schema);

    $this->set_origin_db();

  }

  /**
   * Adds new column to the specified table.
   */
  public function dropField($table_name, $field_name) {
    $this->set_export_db();

    Database::getConnection()->schema()->dropField($table_name, $field_name);

    $this->set_origin_db();

  }

}
