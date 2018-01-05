<?php
namespace Drupal\company\EventSubscriber;

use Drupal\group\GroupMembership;
use Drupal\hook_event_dispatcher\HookEventDispatcherEvents;
use Drupal\hook_event_dispatcher\Event\Entity\EntityPresaveEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class CompanyPresave.
 *
 * @package Drupal\company\EventSubscriber
 */
class CompanyPresave  implements EventSubscriberInterface {

  /**
   * Entity presave event handler.
   *
   * @param \Drupal\hook_event_dispatcher\Event\Entity\EntityPresaveEvent $event
   */
  public function handleEvent(EntityPresaveEvent $event) {
    $entity = $event->getEntity();

    if ($entity->getEntityTypeId() === 'group' && $entity->bundle() === 'company') {

      // TODO: If entity is new, create taxonomy term in 'Affiliate Group' and store it in the field_affiliate_group.

    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[HookEventDispatcherEvents::ENTITY_PRE_SAVE][] = ['handleEvent'];
    return $events;
  }

}