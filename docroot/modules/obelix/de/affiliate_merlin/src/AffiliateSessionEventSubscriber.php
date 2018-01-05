<?php

/**
 * @file
 * Contains \Drupal\affiliate_merlin\AffiliateSessionEventSubscriber.
 */

namespace Drupal\affiliate_merlin;

use Drupal\affiliate\Event\AffiliateSessionEvent;
use Drupal\affiliate\Event\AffiliateSessionEvents;
use Drupal\Core\Url;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Provides a AffiliateSessionEvent Subscriber.
 */
class AffiliateSessionEventSubscriber implements EventSubscriberInterface {

  /**
   * A set of query parameters that should be removed before redirect.
   *
   * @var array
   */
  private $clean_query_values = ['refid'];

  /**
   * @param \Drupal\affiliate\Event\AffiliateSessionEvent $event
   */
  public function onAffiliateStored(AffiliateSessionEvent $event) {
    if ($event->getAffiliateItemType() != 'merlin') {
      // We only want to handle old merlin cms affiliates.
      return;
    }

    if ($event->getAffiliateItem()->field_redirect->isEmpty()) {
      // No redirect by affiliate item provided.
      // In this case we clean all possible old affiliate paramters from request
      // url and redirect user to the cleand url.
      $url = Url::createFromRequest(\Drupal::request());

      $query = [];

      foreach (\Drupal::request()->query as $key => $value) {
        if (!in_array($key, $this->clean_query_values)) {
          $query[$key] = $value;
        }
      }

      $url->setOption('query', $query);

      // Build url string.
      $url = $url->toString();
    }
    else {
      // Set redirect to affiliates redirect destination.
      $uri = $event->getAffiliateItem()->field_redirect[0]->uri;
      $url = Url::fromUri($uri)->toString();

      // Hotfix: Removing random index.php prefix.
      $url = preg_replace('/^\/index\.php/', '', $url);
    }

    $response = new RedirectResponse($url, 301);
    $response->send();
  }

  /**
   * {@inheritdoc}
   */
  static function getSubscribedEvents() {
    $events[AffiliateSessionEvents::STORED][] = ['onAffiliateStored', 2048];
    return $events;
  }
}
