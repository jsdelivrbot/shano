<?php

namespace Drupal\fs_inxmail_bridge\EventSubscriber;

use Drupal\Core\Field\FieldItemList;
use Drupal\forms_suite\DataHandler;
use Drupal\forms_suite\Event\FormSaveEvent;
use Drupal\forms_suite\Event\SubmissionProcessEvent;
use Drupal\forms_suite\Event\SubmissionProcessEvents;
use Drupal\inxmail\InxmailService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


/**
 * Class SubmissionSendSubscriber.
 *
 * @package Drupal\fs_affiliate_bridge
 */
class SubmissionSendSubscriber implements EventSubscriberInterface
{
  /**
   * @var InxmailService
   */
  private $inxmail;

  /**
   * SubmissionSendSubscriber constructor.
   * @param InxmailService $inxmail
   */
  public function __construct(InxmailService $inxmail)
  {
    $this->inxmail = $inxmail;
  }

  /**
   * Send data to inxmail on submission process.
   *
   * @param SubmissionProcessEvent $event
   */
  public function formMailSendSubmission(SubmissionProcessEvent $event)
  {

    $message = $event->getMessage();

    $data_handler = new DataHandler($message->getFields(), $message);
    $values = [];
    foreach ($data_handler->searchFields($message->getFields()) as $field_name => $field) {
      /** @var FieldItemList $field */
      $values[$field_name] = $field->getValue()[0];
    }

    // Send newsletter mail if required.
    if ($event->getForm()->id() == 'newsletter' && !empty($values['field_newsletter_email']['value'])) {
      $this->inxmail->send([
        'email' => $values['field_newsletter_email']['value'],
      ]);
    }

    $event->setMessage($message);
  }


  /**
   * {@inheritdoc}
   */
  static function getSubscribedEvents(){
    if (defined('Drupal\forms_suite\Event\SubmissionProcessEvents::PROCESS_MAIL_SEND')) {
      $events[SubmissionProcessEvents::PROCESS_MAIL_SEND][] = ['formMailSendSubmission', 1000];
      return $events;
    }
  }


}
