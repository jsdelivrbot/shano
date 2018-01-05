<?php

namespace Drupal\map;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Map entity.
 *
 * @see \Drupal\map\Entity\Map.
 */
class MapAccessControlHandler extends EntityAccessControlHandler {
  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\map\MapInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished map entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published map entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit map entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete map entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add map entities');
  }

}
