<?php

namespace Drupal\wv_simma_connector\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use Drupal\node\NodeInterface;

/**
 * Manages child iVision ID property.
 *
 * @MigrateProcessPlugin(
 *   id = "wvi_ivision_id"
 * )
 */
class IvisionID extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    // Note that Simma isn't related with Ivision ID at all, so we need to fool the system to avoid the refactoring
    // on current state, so we set external DB id if child isn't imported yet, otherwise we set already imported
    // child entity id, as ivision id is child id on DE site, otherwise it will cause fatal error if existing entity
    // will be imported with another ID.
    $entity_type = 'child';

    $storage = \Drupal::entityTypeManager()->getStorage($entity_type);

    switch (TRUE) {
      // If entity wasn't imported yet publish it by default.
      case !$id_map = $row->getIdMap():
      case empty($id_map['destid1']):
        // If by some reason external ID equal local child id update it until such node doesn't exist.
        while ($child = $storage->load($value)) {
          // Use 1000 step to decrease the conflict iterations if we crossing Drupal data pool
          $value += 1000;
        }
        return $value;

      default:
        return $id_map['destid1'];
    }

  }

}
