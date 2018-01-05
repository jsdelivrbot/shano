<?php

namespace Drupal\giftshop\EventSubscriber;

use Drupal\forms_suite\Controller\SubmissionProcessController;
use Drupal\forms_suite\Event\FormSendEvent;
use Drupal\forms_suite\Event\FormSendEvents;
use Drupal\giftshop\GiftshopCartInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\forms_suite\Entity\Message;


/**
 * Class FormSaveSubscriber.
 *
 * @package Drupal\giftshop
 */
class FormSendSubscriber implements EventSubscriberInterface
{

  /**
   * @var GiftshopCartInterface
   */
  private $giftshop_cart;

  public function __construct(GiftshopCartInterface $giftshop_cart)
  {
    $this->giftshop_cart = $giftshop_cart;
  }

  /**
   * Form send successful.
   *
   * @param FormSendEvent $event
   */
  public function formSendSuccess(FormSendEvent $event)
  {

    $form = $event->getForm();
    $form_id = $form->getOriginalId();

    // empty gift card
    if ($form_id == 'das_gute_geschenk_checkout') {
      $this->giftshop_cart->emptyCart();
    }
    // Get the messages with status -3 and send emails.
    $entity_type = \Drupal::getContainer()->get('entity_type.manager')->getStorage('forms_message');
    $messages = $entity_type->loadByProperties([
      'transfer_id' => -3,
    ]);
    $renderer = \Drupal::service('renderer');
    $session = \Drupal::service('session');
    $mail_handler = \Drupal::service('forms.mail_handler');
    $submission_handler = new SubmissionProcessController($renderer, $session, $mail_handler);
    foreach ($messages as $message) {
      /** @var Message $message */
      $form_entity = $message->getForm();
      $submission_handler->sendSingleMail($message, $form_entity);
    }
  }

  /**
   * {@inheritdoc}
   */
  static function getSubscribedEvents()
  {
    if (defined('Drupal\forms_suite\Event\FormSendEvents::FORM_SEND_SUCCESS')) {
      $events[FormSendEvents::FORM_SEND_SUCCESS][] = ['formSendSuccess', 1000];
      return $events;
    }
  }


}
