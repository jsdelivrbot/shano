<?php

/**
 * @file
 * Contains \Drupal\forms_suite\Controller\SubmissionProcessController.
 */

namespace Drupal\forms_suite\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Field\FieldItemList;
use Drupal\forms_suite\DataHandler;
use Drupal\forms_suite\Entity\Form;
use Drupal\Core\Render\RendererInterface;
use Drupal\forms_suite\Entity\Message;
use Drupal\forms_suite\Event\SubmissionProcessEvent;
use Drupal\forms_suite\Event\SubmissionProcessEvents;
use Drupal\forms_suite\FormInterface;
use Drupal\forms_suite\MailHandlerInterface;
use Drupal\forms_suite\MessageInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Controller to handle processes for the submissions.
 */
class SubmissionProcessController extends ControllerBase
{

  /**
   * The renderer.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * The session.
   *
   * @var Session
   */
  protected $session;

  /**
   * The forms mail handler service.
   *
   * @var \Drupal\forms_suite\MailHandlerInterface
   */
  protected $mailHandler;

  /**
   * Constructs a FormsController object.
   *
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The renderer.
   * @param Session $session
   * @param MailHandlerInterface $mail_handler
   */
  public function __construct(RendererInterface $renderer, Session $session, MailHandlerInterface $mail_handler)
  {
    $this->renderer = $renderer;
    $this->session = $session;
    $this->mailHandler = $mail_handler;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container)
  {
    return new static(
      $container->get('renderer'),
      $container->get('session'),
      $container->get('forms.mail_handler')
    );
  }

  /**
   * Crawls all messages which are not send to iVision.
   * Could have two reasons.
   * - External payment process finished.
   * - Problems with the send process tot the iVision API.
   * @param bool $output
   * @return array
   */
  public function checkSubmissionsToSend($output = TRUE)
  {

    $messages = $this->getAllSubmissions();

    $count = 0;
    foreach ($messages as $message) {
      /** @var Message $message */
      /** @var Form $form_entity */
      $form_entity = $message->getForm();
      $values = [];

      $data_handler = new DataHandler($message->getFields(), $message);

      foreach ($data_handler->searchFields($message->getFields()) as $field_name => $field) {
        /** @var FieldItemList $field */
        $values[$field_name] = $field->getValue()[0];
      }
      $transfer_id_original = $message->getTransferID();
      $message->setTransferID(0);

      // event dispatcher pre save
      $event = new SubmissionProcessEvent($message, $form_entity);
      $eventDispatcher = \Drupal::service('event_dispatcher');
      $eventDispatcher->dispatch(SubmissionProcessEvents::PROCESS_PRE_SAVE, $event);

      if($message->getTransferID() !== -1){
        $count++;
      }
      $message->save();

      if($transfer_id_original !== '-5') {
        $this->processMails($message, $form_entity);
      }

    }

    if ($output) {
      return [
        '#type' => 'markup',
        '#markup' => $this->t('Send @count form submissions to iVision.', ['@count' => $count]),
      ];
    }

  }


  /**
   * @param MessageInterface $message
   * @param FormInterface $form_entity
   */
  private function processMails(MessageInterface $message, FormInterface $form_entity)
  {

    $data_handler = new DataHandler($message->getFields(), $message);
    $values = [];
    foreach ($data_handler->searchFields($message->getFields()) as $field_name => $field) {
      /** @var FieldItemList $field */
      $values[$field_name] = $field->getValue()[0];
    }

    // do not send emails if we have an iVision error or if it had a transaction id -4.
    // only mail if it is not a children(no referenced entity)
    if (
      $message->getTransferID() != -1 &&
      !array_key_exists($message->id(), $this->getAllSubmissions([-4])) &&
      $message->getReferencedEntity() == NULL
    ) {

      // send mail if required.
      if (!empty($form_entity->getReply())) {
        $user = $this->currentUser();
        $this->mailHandler->sendMailMessages($message, $user, $data_handler, $values);
      }

      // send data mail if required.
      if (!empty($form_entity->getRecipients())) {
        $user = $this->currentUser();
        $this->mailHandler->sendDataMail($message, $user, $values);
      }


      // event dispatcher pre save
      $event = new SubmissionProcessEvent($message, $form_entity);
      $eventDispatcher = \Drupal::service('event_dispatcher');
      $eventDispatcher->dispatch(SubmissionProcessEvents::PROCESS_MAIL_SEND, $event);

    }
  }


  /**
   * Returns all submission.
   *'
   * @param array $custom_message_list
   * @return array Array with Message objects.
   * Array with Message objects.
   */
  private function getAllSubmissions(array $custom_message_list = [])
  {
    $message_entity_type = $this->entityTypeManager()
      ->getStorage('forms_message');

    // if empty set all message transfer_id
    if (empty($custom_message_list)) {
      $custom_message_list = [-1, -2, -3, -4, -5];
    }

    $messages_external_payment = [];
    if (in_array(-2, $custom_message_list)) {
      // get messages with finished external payment process but unfinished iVision send process
      $messages_external_payment = $message_entity_type->loadByProperties([
        'external_payment' => 1,
        'transfer_id' => -2,
      ]);
    }

    $messages_send_fails = [];
    if (in_array(-1, $custom_message_list)) {
      // get messages with finished external payment process but unfinished iVision send process
      $messages_send_fails = $message_entity_type->loadByProperties([
        'transfer_id' => -1,
      ]);
    }

    $messages_no_external_payment = [];
    if (in_array(-3, $custom_message_list)) {
      // get messages with finished external payment process but unfinished iVision send process
      $messages_no_external_payment = $message_entity_type->loadByProperties([
        'transfer_id' => -3,
      ]);
    }

    $messages_no_email_notification = [];
    if (in_array(-4, $custom_message_list)) {
      // give you the possibility to send data to iVision without e-mail notification to the user.
      $messages_no_email_notification = $message_entity_type->loadByProperties([
        'transfer_id' => -4,
      ]);
    }

    $messages_email_already_sent = [];
    if (in_array(-5, $custom_message_list)) {
      // Send data to iVision without e-mail notification to the user.
      $messages_email_already_sent = $message_entity_type->loadByProperties([
        'transfer_id' => -5,
      ]);
    }

    return array_merge($messages_external_payment, $messages_send_fails, $messages_no_external_payment, $messages_no_email_notification, $messages_email_already_sent);

  }

  /**
   * @param \Drupal\forms_suite\MessageInterface $message
   * @param \Drupal\forms_suite\FormInterface $form_entity
   */
  public function sendSingleMail(MessageInterface $message, FormInterface $form_entity) {
    $message->setTransferID(-5)->save();
    $this->processMails($message, $form_entity);
  }

}
