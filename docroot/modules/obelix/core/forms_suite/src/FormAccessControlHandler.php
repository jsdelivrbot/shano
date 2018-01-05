<?php

/**
 * @file
 * Contains \Drupal\forms_suite\FormAccessControlHandler.
 */

namespace Drupal\forms_suite;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Defines the access control handler for the forms form entity type.
 *
 * @see \Drupal\forms_suite\Entity\Form.
 */
class FormAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    if ($operation == 'view') {
      // Do not allow access personal form via site-wide route.
      return AccessResult::allowedIf($account->hasPermission('access site-wide forms') && $entity->id() !== 'personal')->cachePerPermissions();
    }
    elseif ($operation == 'delete' || $operation == 'update') {
      // Do not allow the 'personal' form to be deleted, as it's used for
      // the personal forms form.
      return AccessResult::allowedIf($account->hasPermission('administer forms') && $entity->id() !== 'personal')->cachePerPermissions();
    }

    return parent::checkAccess($entity, $operation, $account);
  }

}
