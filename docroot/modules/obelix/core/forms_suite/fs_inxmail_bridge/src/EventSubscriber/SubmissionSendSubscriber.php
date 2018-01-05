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

    // send newsletter mail if required.
    if (isset($values['field_newsletter']) && $values['field_newsletter']['newsletter']) {
      // Get the right field_user field.
      if (!empty($values['field_user_data'])) {
        $field_user = $values['field_user_data'];
      }
      else {
        $field_user = $values['field_user'];
      }
      if ($this->inxmail) {
        switch ($field_user['salutation']) {
          case 1:
            $gender = "m";
            break;
          case 2:
            $gender = "f";
            break;
          default:
            $gender = "";
            break;
        }
        $this->inxmail->send([
          'geschlecht' => $gender,
          'vorname' => $field_user['firstName'],
          'nachname' => $field_user['surname'],
          'email' => $field_user['email'],
        ]);
      }
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
