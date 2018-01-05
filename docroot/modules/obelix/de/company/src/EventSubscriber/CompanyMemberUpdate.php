<?php
namespace Drupal\company\EventSubscriber;

use Drupal\group\GroupMembership;
use Drupal\hook_event_dispatcher\HookEventDispatcherEvents;
use Drupal\hook_event_dispatcher\Event\Entity\EntityPresaveEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class CompanyMemberUpdate.
 *
 * @package Drupal\company\EventSubscriber
 */
class CompanyMemberUpdate  implements EventSubscriberInterface {

  /**
   * Entity presave event handler.
   *
   * @param \Drupal\hook_event_dispatcher\Event\Entity\EntityPresaveEvent $event
   */
  public function handleEvent(EntityPresaveEvent $event) {
    $entity = $event->getEntity();

    if ($entity->getEntityTypeId() === 'group_content' && $entity->bundle() === 'company-group_membership') {
      $group_membership = new GroupMembership($entity);
      $user = $group_membership->getUser();
      $broker_pages = $this->getBrokerPage($user);
      $entity_original = $entity->original;

      if ($entity_original === NULL) {
        return;
      }

      $roles_original = $entity_original->get('group_roles')->referencedEntities();
      $roles = $entity->get('group_roles')->referencedEntities();

      // broker role removed
      if ($this->isBroker($roles_original) && $this->isNotBroker($roles)) {
        // block user
        $user->set('status', 0);
        $user->save();

        // unpublish broker page
        foreach ($broker_pages as $broker_page) {
          $broker_page->set('status', 0);
          $broker_page->save();
        }
      }

      // broker role added
      if ($this->isNotBroker($roles_original) && $this->isBroker($roles)) {
        // activate user
        $user->set('status', 1);
        $user->save();

        //publish broker page
        foreach ($broker_pages as $broker_page) {
          $broker_page->set('status', 1);
          $broker_page->save();
        }
      }

    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[HookEventDispatcherEvents::ENTITY_PRE_SAVE][] = ['handleEvent'];
    return $events;
  }

  /**
   * Flatten roles.
   *
   * @param array
   *   The array of roles entity.
   *
   * @return array
   *   The array of roles labels.
   */
  private function flattenRoles($roles) {
    foreach ($roles as $key_role => $role) {
      $roles[$key_role] = $role->label();
    }
    return $roles;
  }

  /**
   * Check if the broker role is present.
   *
   * @param array
   *   The array of roles entity.
   *
   * @return boolean
   *   Broker role is present boolean.
   */
  private function isBroker($roles) {
    $roles = $this->flattenRoles($roles);
    return (array_search('broker', $roles) !== NULL);
  }

  /**
   * Check if the broker role is not present.
   *
   * @param array
   *   The array of roles entity.
   *
   * @return boolean
   *   Broker role is not present boolean.
   */
  private function isNotBroker($roles) {
    $roles = $this->flattenRoles($roles);
    return (array_search('broker', $roles) === FALSE);
  }

  /**
   * Get the broker page from user.
   *
   * @param \Drupal\entity\user
   *   The user entity.
   *
   * @return \Drupal\node\Entity\Node
   *   Broker page entity.
   */
  private function getBrokerPage($user) {
    $query = \Drupal::database()->select('node_field_data', 'n');
    $query->fields('n', ['nid']);
    $query->condition('n.type', 'broker_page');
    $query->condition('n.uid', $user->uid->value);

    $query_results = $query->execute()->fetchAll();
    $broker_page_ids = [];
    foreach ($query_results as $item) {
      $broker_page_ids[] = $item->nid;
    }

    return \Drupal::entityTypeManager()->getStorage('node')->loadMultiple($broker_page_ids);
  }


}