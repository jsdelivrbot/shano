<?php

namespace Drupal\child;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Child entity.
 *
 * @see \Drupal\child\Entity\Child.
 */
class ChildAccessControlHandler extends EntityAccessControlHandler {
  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\child\ChildInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished child entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published child entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit child entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete child entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add child entities');
  }

}
