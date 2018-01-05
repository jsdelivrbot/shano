<?php

/**
 * @file
 * Contains \Drupal\forms_suite\MailHandlerInterface.
 */

namespace Drupal\forms_suite;

use Drupal\Core\Session\AccountInterface;

/**
 * Provides an interface for assembly and dispatch of forms mail messages.
 */
interface MailHandlerInterface {

  /**
   * Sends mail messages as appropriate for a given Message form submission.
   *
   * Can potentially send up to three messages as follows:
   * - To the configured recipient;
   * - Auto-reply to the sender; and
   * - Carbon copy to the sender.
   *
   * @param \Drupal\forms_suite\MessageInterface $message
   *   Submitted message entity.
   * @param \Drupal\Core\Session\AccountInterface $sender
   *   User that submitted the message entity form.
   *
   * @param DataHandlerInterface $data_handler
   * @param array $values
   * @return When unable to determine message recipient.
   * When unable to determine message recipient.
   */
  public function sendMailMessages(MessageInterface $message, AccountInterface $sender, DataHandlerInterface $data_handler, array $values);

  /**
   * Sends a data mail to the recipient list.
   *
   * @param MessageInterface $message
   * @param AccountInterface $sender
   * @param array $values
   * @return mixed
   */
  public function sendDataMail(MessageInterface $message, AccountInterface $sender, array $values);
}
