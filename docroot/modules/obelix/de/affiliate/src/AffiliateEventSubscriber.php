<?php

/**
 * @file
 * Contains \Drupal\affiliate\AffiliateEventSubscriber.
 */

namespace Drupal\affiliate;

use Symfony\Component\HttpKernel\Event\FinishRequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\affiliate\Event\AffiliateSessionEvents;
use Drupal\affiliate\Event\AffiliateSessionEvent;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Provides an event subscriber.
 */
class AffiliateEventSubscriber implements EventSubscriberInterface {

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
    if ($event->getAffiliateItem()->field_redirect->isEmpty()) {
      // No redirect by affiliate item provided.
      // In this case we clean all possible old affiliate parameters from
      // request url and redirect user to the cleaned url.
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
   * Kernel Request Finished event handler.
   *
   * @param \Symfony\Component\HttpKernel\Event\FinishRequestEvent $event
   */
  public function onKernelRequestFinished(FinishRequestEvent $event) {
    // Only handle on master requests.
    if (!$event->isMasterRequest()) {
      return;
    }

    $affiliateItem = \Drupal::entityTypeManager()
      ->getStorage('affiliate')
      ->load($event->getRequest()->get('refid', 0));

    if ($affiliateItem && $affiliateItem->access('store_in_session')) {
      // Store affiliate item.
      \Drupal::service('affiliate_session_manager')->storeItem($affiliateItem);
    }
  }

  /**
   * {@inheritdoc}
   */
  static function getSubscribedEvents() {
    $events[KernelEvents::FINISH_REQUEST][] = ['onKernelRequestFinished', 2048];
    $events[AffiliateSessionEvents::STORED][] = ['onAffiliateStored', 2048];
    return $events;
  }
}
