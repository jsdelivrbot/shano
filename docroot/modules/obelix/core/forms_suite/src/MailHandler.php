<?php

/**
 * @file
 * Contains \Drupal\forms_suite\MailHandler.
 */

namespace Drupal\forms_suite;

use Drupal\child\Entity\Child;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Field\FieldItemList;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Mail\MailManagerInterface;
use Drupal\Core\Render\Renderer;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\Core\Url;
use Drupal\file\Entity\File;
use Drupal\forms_suite\Entity\Message;
use Drupal\forms_suite\Event\MailHandlerEvent;
use Drupal\forms_suite\Event\MailHandlerEvents;
use Drupal\node\Entity\Node;
use Psr\Log\LoggerInterface;

/**
 * Provides a class for handling assembly and dispatch of forms mail messages.
 */
class MailHandler implements MailHandlerInterface
{

  /**
   * Language manager service.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * Mail manager service.
   *
   * @var \Drupal\Core\Mail\MailManagerInterface
   */
  protected $mailManager;

  /**
   * The user entity storage handler.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $userStorage;

  /**
   * Constructs a new \Drupal\forms_suite\MailHandler object.
   *
   * @param \Drupal\Core\Mail\MailManagerInterface $mail_manager
   *   Mail manager service.
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   Language manager service.
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   *   Entity manager service.
   */
  public function __construct(MailManagerInterface $mail_manager, LanguageManagerInterface $language_manager, EntityManagerInterface $entity_manager)
  {
    $this->languageManager = $language_manager;
    $this->mailManager = $mail_manager;
    $this->userStorage = $entity_manager->getStorage('user');
  }

  /**
   * {@inheritdoc}
   */
  public function sendMailMessages(MessageInterface $message, AccountInterface $sender, DataHandlerInterface $data_handler, array $values)
  {
    $params = array();

    // event dispatcher
    $event = new MailHandlerEvent($message, $data_handler, $values, $params);
    $eventDispatcher = \Drupal::service('event_dispatcher');

    $eventDispatcher->dispatch(MailHandlerEvents::MAIL_MESSAGE_PRE_PROCESS_PARAMS, $event);

    // Clone the sender, as we make changes to mail and name properties.
    $sender_cloned = clone $this->userStorage->load($sender->id());

    $recipient_langcode = $this->languageManager->getDefaultLanguage()->getId();
    $form = $message->getForm();

    $form->setReply(nl2br($data_handler->getReply()));
    $template = $form->getTemplateEmail();

    // check alternative auto reply text with condition
    if (!empty($form->getAlternativeConditionVar())) {
      $token_service = \Drupal::token();
      $placeholder_values = $data_handler->getPlaceholderValues($data_handler->convertFieldKeys($data_handler->getFields()));
      $condition_var = $token_service->replace($form->getAlternativeConditionVar(), $placeholder_values);
      if ($condition_var == $form->getAlternativeConditionResult()) {
        $form->setReply(nl2br($data_handler->getReply($form->getAlternativeReply())));
        $template = $form->getAlternativeTemplateEmail();
      }
    }

    // Build email parameters.
    $params['forms_message'] = $form;
    $params['sender'] = $sender_cloned;
    $params['subject'] = $form->getSubject();
    $params['markup'] = $form->getReply();
    if ($template !== 0) {
      $params['theme'] = $template;
    }

    // Send to the form recipient(s), using the site's default language.
    $params['form'] = $form;

    $event->setParams($params);
    $eventDispatcher->dispatch(MailHandlerEvents::MAIL_MESSAGE_POST_PROCESS_PARAMS, $event);

    $to = '';
    foreach ($values as $field) {
      if (array_key_exists('email', $field)) {
        $to = $field['email'];
      }
    }

    // Send email to the recipient(s).
    $this->mailManager->mail('forms_suite', 'donation', $to, $recipient_langcode, $params, $sender_cloned->getEmail());
  }


  /**
   * {@inheritdoc}
   */
  public function sendDataMail(MessageInterface $message, AccountInterface $sender, array $values)
  {

    $sender_cloned = clone $this->userStorage->load($sender->id());
    $recipient_langcode = $this->languageManager->getDefaultLanguage()->getId();
    $form = $message->getForm();
    $to = implode(', ', $form->getRecipients());

    $values['tracking'] = [
      'motivation_code' => $form->getMotivationCode(),
      'designation_id' => $form->getDesignationID()
    ];

    if (!empty($message->getMotivationCode())) {
      $values['tracking']['motivation_code'] = $message->getMotivationCode();
    }
    if (!empty($form->getAdditionalTracking())) {
      // have to change to real values
      $values['tracking']['productCode'] = $form->getAdditionalTracking();
    }

    // Build email parameters.
    $params = [
      'values' => $values,
      'subject' => 'Submitted Form: ' . $form->label(),
      'theme' => 'data_mail',
    ];

    // Send email to the recipient(s).
    $this->mailManager->mail('forms_suite', 'data_mail', $to, $recipient_langcode, $params, $sender_cloned->getEmail());

  }

}
