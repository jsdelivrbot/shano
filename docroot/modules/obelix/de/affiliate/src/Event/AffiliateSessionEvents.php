<?php

namespace Drupal\affiliate\Event;

final class AffiliateSessionEvents {

  /**
   * The STORED event occurs once an affiliate item is stored to session.
   *
   * This event allows you to run expensive post-response jobs.
   * The event listener method receives a
   * \Drupal\affiliate\Event\AffiliateSessionEvent
   *
   * @Event
   *
   * @var string
   */
  const STORED = 'affiliate_session_item_stored';

  /**
   * The RESTORED event occurs once an affiliate item is restored from session.
   *
   * This event allows you to run expensive post-response jobs.
   * The event listener method receives a
   * \Drupal\affiliate\Event\AffiliateSessionEvent
   *
   * @Event
   *
   * @var string
   */
  const RESTORED = 'affiliate_session_item_restored';
}
