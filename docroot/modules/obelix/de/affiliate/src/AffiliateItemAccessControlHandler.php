<?php

namespace Drupal\affiliate;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Affiliate entity.
 *
 * @see \Drupal\affiliate\Entity\AffiliateItem.
 */
class AffiliateItemAccessControlHandler extends EntityAccessControlHandler {
  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\affiliate\AffiliateItemInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished affiliate entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published affiliate entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit affiliate entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete affiliate entities');

      case 'store_in_session':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'store unpublished affiliate entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'store published affiliate entities');
    }

    // Unknown operation, forbidden.
    return AccessResult::forbidden();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add affiliate entities');
  }

}
