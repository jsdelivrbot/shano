<?php

namespace Drupal\fs_affiliate_bridge\EventSubscriber;

use Drupal\affiliate\Entity\AffiliateItem;
use Drupal\forms_suite\Event\FormEvents;
use Drupal\forms_suite\Event\FormSaveEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


/**
 * Class FormSaveSubscriber.
 *
 * @package Drupal\fs_affiliate_bridge
 */
class FormSaveSubscriber implements EventSubscriberInterface {


  /**
   * Set affiliate tracking data.
   *
   * @param FormSaveEvent $event
   */
  public function formPreSaveSubmission(FormSaveEvent $event){
    $message = $event->getMessage();

    // Setting motivation code from base form entity.
    if ($motivation_code = $message->getForm()->getMotivationCode()) {
      $message->setMotivationCode($motivation_code);
    }

    // Set designation code from base form entity.
    if ($designation_code = $message->getForm()->getDesignationID()) {
      $message->setDesignationCode($designation_code);
    }

    // Override tracking by affiliate system.
    /** @var AffiliateItem $affiliateItem */
    if ($affiliateItem = \Drupal::service('affiliate_session_manager')->restoreItem()) {
      $message->setAffiliateID($affiliateItem->id());
      if ($motivation_code = $affiliateItem->getMotivationCode()) {
        $message->setMotivationCode($motivation_code);
      }
    }

    $event->setMessage($message);
  }


  /**
   * {@inheritdoc}
   */
  static function getSubscribedEvents()
  {
    if (defined('Drupal\forms_suite\Event\FormEvents::SUBMITTED_PRE_SAVE')) {
      $events[FormEvents::SUBMITTED_PRE_SAVE][] = ['formPreSaveSubmission', 1100];
      return $events;
    }
  }


}
