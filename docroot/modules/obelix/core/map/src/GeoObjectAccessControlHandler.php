<?php

namespace Drupal\map;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Geo object entity.
 *
 * @see \Drupal\map\Entity\GeoObject.
 */
class GeoObjectAccessControlHandler extends EntityAccessControlHandler {
  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\map\GeoObjectInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished geo object entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published geo object entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit geo object entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete geo object entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add geo object entities');
  }

}
