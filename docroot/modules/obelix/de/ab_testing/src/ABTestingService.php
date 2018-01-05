<?php

namespace Drupal\ab_testing;

use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Session\SessionManagerInterface;
use Drupal\Core\Url;
use Drupal\user\PrivateTempStoreFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ABTestingService.
 *
 * @package Drupal\ab_testing
 */
class ABTestingService implements ABTestingServiceInterface {

  /**
   * The private temp store for the temporary item.
   *
   * @var \Drupal\user\PrivateTempStore $storage
   */
  protected $storage;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('user.private_tempstore'),
      $container->get('session_manager'),
      $container->get('current_user')
    );
  }

  /**
   * ABTestingTempItem constructor.
   *
   * @param \Drupal\user\PrivateTempStoreFactory $privateTempStore
   * @param \Drupal\Core\Session\SessionManagerInterface $sessionManager
   * @param \Drupal\Core\Session\AccountInterface $currentUser
   */
  public function __construct(PrivateTempStoreFactory $privateTempStore, SessionManagerInterface $sessionManager, AccountInterface $currentUser) {
    $this->storage = $privateTempStore->get('ab_testing');

    // @TODO - This implementation should be reworked to set a Cookie instead
    // of what it does now. It was agreed that this functionality should be
    // reworked entirely.

    // Start a manual session for anonymous users.
    if ($currentUser->isAnonymous() && !isset($_SESSION['ab_testing'])) {
      $_SESSION['ab_testing'] = TRUE;
      $sessionManager->start();
    }
  }


  /**
   * {@inheritdoc}
   */
  public function set($id, &$data = NULL, $default = 'a') {

    switch ($id) {
      // 1/2 child finder sponsorship test.
      case 2 :
        $config = \Drupal::config('wv_site.settings.child_finder');

        // Don't change redirect if simple child finder process is used.
        if ($this->setUserGroup(1) != $default && !$config->get('is_simple_find_process')) {

          if ($data['values']['gender'] === '0' || $data['values']['country'] === '0') {
            $data['url'] = Url::fromUserInput('/forms/child_sponsorship_direct');
          }
          else {
            $data['url'] = Url::fromUserInput('/forms/child_sponsorship_with_condition');
          }
        }
        break;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getUserGroup() {
    if (!empty($this->storage->get('user_group'))) {
      return $this->storage->get('user_group');
    }
    else {
      return NULL;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function setUserGroup($rule_id, $overwrite = NULL) {
    if($overwrite !== NULL){
      $this->storage->set('user_group', $overwrite);
      return $overwrite;
    }

    $rand = mt_rand(1, 100);
    if ($this->getUserGroup() === NULL) {
      foreach ($this->getSelectionRule($rule_id) as $group => $probability) {
        if ($rand >= $probability[0] && $rand <= $probability[1]) {
          $this->storage->set('user_group', $group);
          return $group;
        }
      }
    }
    return $this->getUserGroup();
  }

  /**
   * {@inheritdoc}
   */
  public function getSelectionRule($rule_id) {
    switch ($rule_id) {
      case  1:
        return [
          'a' => [1, 50],
          'b' => [51, 100],
        ];
        break;
      case  2:
        return [
          'a' => [1, 33],
          'b' => [34, 66],
          'c' => [67, 100],
        ];
        break;
    }
  }
}
