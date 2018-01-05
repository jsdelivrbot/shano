<?php

/**
 * @file
 * Contains \Drupal\wv_simma_connector\Plugin\migrate\source\Children.
 */

namespace Drupal\wv_simma_connector\Plugin\migrate\source;

use Drupal\Core\State\StateInterface;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\wv_simma_connector\Plugin\migrate\source\MSSql;
use Drupal\wv_simma_connector\Plugin\migrate\Row;

/**
 * Extract children from MSSql Simma database.
 *
 * @MigrateSource(
 *   id = "children"
 * )
 */
class Children extends MSSql {

  /**
   * {@inheritdoc}
   */
  public function query() {
    // Select published posts.
    $query = $this->select('children', 'c')
      ->fields('c', array_keys($this->childFields()));

    return $query;
  }

  /**
   * Returns the child fields to be migrated.
   *
   * @return array
   *   Associative array having field name as key and description as value.
   */
  protected function childFields() {
    $fields = array(
      'id' => $this->t('ID'),
      'birthdate' => $this->t('Birthdate'),
      'brothers' => $this->t('Brothers'),
      'childsequence' => $this->t('Child Sequence'),
      'video_url' => $this->t('Video Url'),
      'image' => $this->t('Image'),
      'choredesc' => $this->t('Choredesc'),
      'familyname' => $this->t('Family Name'),
      'fatherjobstatusdesc' => $this->t('Father Job Status Desc'),
      'fatherstatusdesc' => $this->t('Father Status Desc'),
      'favouritesubjectdesc' => $this->t('Favourite Subject Desc'),
      'genderdesc' => $this->t('Gender Desc'),
      'givenname' => $this->t('Given Name'),
      'gradenumber' => $this->t('Grade Number'),
      'handicapdesc' => $this->t('Handicap Desc'),
      'healthdesc' => $this->t('Health Desc'),
      'liveswithdesc' => $this->t('Lives With Desc'),
      'localchildfamilyname' => $this->t('Local Child Family Name'),
      'localchildgivenname' => $this->t('Local Child Given Name'),
      'motherjobstatusdesc' => $this->t('Mother Job Status Desc'),
      'motherstatusdesc' => $this->t('Mother Status Desc'),
      'noschoolreasondesc' => $this->t('No School Reason Desc'),
      'peoplegroupdesc' => $this->t('People Group Desc'),
      'playdesc' => $this->t('Play Desc'),
      'child_project' => $this->t('Project'),
      'schoolleveldesc' => $this->t('School Level Desc'),
      'sisters' => $this->t('Sisters'),
      'reserved' => $this->t('Reserved'),
      'url_alias' => $this->t('Url alias'),
      'status' => $this->t('Status'),
      'childtext1' => $this->t('childtext1'),
      'childtext2' => $this->t('childtext2'),
      'childtext3' => $this->t('childtext3'),
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
  public function prepareRow(\Drupal\Migrate\Row $row) {
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
    return 'child';
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    // Make a given child's mapped key unique. Otherwise the same children can be
    // created if importing table's children ids were changed.
    return array(
      'child_project' => array(
        'type' => 'integer',
      ),
      'childsequence' => array(
        'type' => 'integer',
      ),
    );
  }

  /**
   * {@inheritdoc}
   *
   * The migration iterates over rows returned by the source plugin. This
   * method determines the next row which will be processed and imported into
   * the system.
   *
   * The method tracks the source and destination IDs using the ID map plugin.
   *
   * This also takes care about highwater support. Highwater allows to reimport
   * rows from a previous migration run, which got changed in the meantime.
   * This is done by specifying a highwater field, which is compared with the
   * last time, the migration got executed (originalHighWater).
   */
  public function next() {
    $this->currentSourceIds = NULL;
    $this->currentRow = NULL;

    // In order to find the next row we want to process, we ask the source
    // plugin for the next possible row.
    while (!isset($this->currentRow) && $this->getIterator()->valid()) {

      $row_data = $this->getIterator()->current() + $this->configuration;
      $this->fetchNextRow();
      $row = new Row($row_data, $this->migration->getSourcePlugin()->getIds(), $this->migration->getDestinationIds());

      // Populate the source key for this row.
      $this->currentSourceIds = $row->getSourceIdValues();

      // Pick up the existing map row, if any, unless fetchNextRow() did it.
      if (!$this->mapRowAdded && ($id_map = $this->idMap->getRowBySource($this->currentSourceIds))) {
        $row->setIdMap($id_map);
      }

      // Clear any previous messages for this row before potentially adding
      // new ones.
      if (!empty($this->currentSourceIds)) {
        $this->idMap->delete($this->currentSourceIds, TRUE);
      }

      // Preparing the row gives source plugins the chance to skip.
      if ($this->prepareRow($row) === FALSE) {
        continue;
      }

      // Check whether the row needs processing.
      // 1. This row has not been imported yet.
      // 2. Explicitly set to update.
      // 3. The row is newer than the current highwater mark.
      // 4. If no such property exists then try by checking the hash of the row.
      if (!$row->getIdMap() || $row->needsUpdate() || $this->aboveHighwater($row) || $this->rowChanged($row)) {
        $this->currentRow = $row->freezeSource();
      }

      if ($this->getHighWaterProperty()) {
        $this->saveHighWater($row->getSourceProperty($this->highWaterProperty['name']));
      }
    }
  }
}
