<?php

/**
 * @file
 * Contains \Drupal\editorial_content\EditorialContentAccessControlHandler.
 */

namespace Drupal\editorial_content;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Editorially block entity.
 *
 * @see \Drupal\editorial_content\Entity\EditoriallyBlock.
 */
class EditorialContentAccessControlHandler extends EntityAccessControlHandler {
  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\editorial_content\EditorialContentInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished editorial_content entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published editorial_content entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit editorial_content entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete editorial_content entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add editorial_content entities');
  }

}
