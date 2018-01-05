<?php

/**
 * @file
 * Contains \Drupal\wv_simma_connector\Plugin\migrate\source\Projects.
 */

namespace Drupal\wv_simma_connector\Plugin\migrate\source;

use Drupal\migrate\Row;
use Drupal\wv_simma_connector\Plugin\migrate\source\MSSql;

/**
 * Extract projects from MSSql Simma database.
 *
 * @MigrateSource(
 *   id = "projects"
 * )
 */
class Projects extends MSSql {

  /**
   * {@inheritdoc}
   */
  public function query() {
    // Select published posts.
    $query = $this->select('projects', 'p')
      ->fields('p', array_keys($this->projectFields()));

    return $query;
  }

  /**
   * Returns the project fields to be migrated.
   *
   * @return array
   *   Associative array having field name as key and description as value.
   */
  protected function projectFields() {
    $fields = array(
      'projectid' => $this->t('ID'),
      'description' => $this->t('Description'),
      'country_code' => $this->t('Country Code'),
      'country_description' => $this->t('Country'),
      'projecttext1' => $this->t('Projecttext1'),
//      'field_enddate' => $this->t('End Date'),
//      'field_startdate' => $this->t('Start Date'),
//      'field_numberofdropped' => $this->t('Number of Dropped'),
//      'field_numberofsponsored' => $this->t('Number of Sponsored'),
//      'field_numberofunsponsored' => $this->t('Number of Unsponsored'),
//      'field_totalofchildren' => $this->t('Total of Children'),
    );

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = $this->childFields();

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    return parent::prepareRow($row);
  }

  /**
   * {@inheritdoc}
   */
  public function bundleMigrationRequired() {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function entityTypeId() {
    return 'project';
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return array(
      'projectid' => array(
        'type' => 'integer',
        'alias' => 'p',
      ),
    );
  }
}
