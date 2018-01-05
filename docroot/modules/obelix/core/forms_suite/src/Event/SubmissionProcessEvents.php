<?php

namespace Drupal\forms_suite\Event;

final class SubmissionProcessEvents{

  /**
   * Submission process pre save
   *
   * @Event
   *
   * @var string
   */
  const PROCESS_PRE_SAVE = 'process_pre_save';

  /**
   * Submission process mail send.
   *
   * @Event
   *
   * @var string
   */
  const PROCESS_MAIL_SEND = 'process_mail_send';

}
