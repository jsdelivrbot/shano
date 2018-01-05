<?php

/**
 * @file
 * Contains \Drupal\forms_suite\FormsMessageAccessControlHandler.
 */

namespace Drupal\forms_suite;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Session\AccountInterface;

/**
 * Defines the access control handler for the message form entity type.
 *
 * @see \Drupal\forms_suite\Entity\Message.
 */
class FormsMessageAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'access site-wide forms');
  }
}
