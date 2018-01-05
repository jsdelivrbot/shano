<?php

/**
 * @file
 * Contains \Drupal\forms_suite\Access\FormsPageAccess.
 */

namespace Drupal\forms_suite\Access;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Routing\Access\AccessInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\user\UserDataInterface;
use Drupal\user\UserInterface;

/**
 * Access check for forms_personal_page route.
 */
class FormsPageAccess implements AccessInterface {

  /**
   * The forms settings config object.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The user data service.
   *
   * @var \Drupal\user\UserDataInterface;
   */
  protected $userData;

  /**
   * Constructs a FormsPageAccess instance.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   * @param \Drupal\user\UserDataInterface $user_data
   *   The user data service.
   */
  public function __construct(ConfigFactoryInterface $config_factory, UserDataInterface $user_data) {
    $this->configFactory = $config_factory;
    $this->userData = $user_data;
  }

  /**
   * Checks access to the given user's forms page.
   *
   * @param \Drupal\user\UserInterface $user
   *   The user being formsed.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The currently logged in account.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   */
  public function access(UserInterface $user, AccountInterface $account) {
    $forms_account = $user;

    // Anonymous users cannot have forms forms.
    if ($forms_account->isAnonymous()) {
      return AccessResult::forbidden();
    }

    // Users may not forms themselves by default, hence this requires user
    // granularity for caching.
    $access = AccessResult::neutral()->cachePerUser();
    if ($account->id() == $forms_account->id()) {
      return $access;
    }

    // User administrators should always have access to personal forms forms.
    $permission_access = AccessResult::allowedIfHasPermission($account, 'administer users');
    if ($permission_access->isAllowed()) {
      return $access->orIf($permission_access);
    }

    // If requested user has been blocked, do not allow users to forms them.
    $access->cacheUntilEntityChanges($forms_account);
    if ($forms_account->isBlocked()) {
      return $access;
    }

    // Forbid access if the requested user has disabled their forms form.
    $account_data = $this->userData->get('forms', $forms_account->id(), 'enabled');
    if (isset($account_data) && !$account_data) {
      return $access;
    }

    // If the requested user did not save a preference yet, deny access if the
    // configured default is disabled.
    $forms_settings = $this->configFactory->get('forms.settings');
    $access->cacheUntilConfigurationChanges($forms_settings);
    if (!isset($account_data) && !$forms_settings->get('user_default_enabled')) {
      return $access;
    }

    return $access->orIf(AccessResult::allowedIfHasPermission($account, 'access user forms'));
  }

}
