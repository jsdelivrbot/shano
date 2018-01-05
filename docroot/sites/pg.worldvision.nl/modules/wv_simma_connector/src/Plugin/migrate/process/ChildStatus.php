<?php

namespace Drupal\wv_simma_connector\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use Drupal\node\NodeInterface;

/**
 * Manages child status property (note that children have status string field also).
 *
 * @MigrateProcessPlugin(
 *   id = "wvi_child_status"
 * )
 */
class ChildStatus extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   *
   * Split the 'administer nodes' permission from 'access content overview'.
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $entity_type = 'child';

    $storage = \Drupal::entityTypeManager()->getStorage($entity_type);

    switch (TRUE) {
      // If migration user asks to set status to publish do it without any checks.
      case !empty($this->configuration['force']):
        return NodeInterface::PUBLISHED;

      // If entity wasn't imported yet publish it by default.
      case !$id_map = $row->getIdMap():
      case empty($id_map['destid1']):
      case !$child = $storage->load($id_map['destid1']):
        return NodeInterface::PUBLISHED;

    }

    // If child was imported before don't change it's status as it can be blocked or unpublished by hand.
    return NULL;

  }

}
