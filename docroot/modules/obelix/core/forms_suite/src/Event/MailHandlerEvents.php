<?php

namespace Drupal\forms_suite\Event;

final class MailHandlerEvents{

  /**
   * Mail handler sendMailMessage pre process params.
   *
   * @Event
   *
   * @var string
   */
  const MAIL_MESSAGE_PRE_PROCESS_PARAMS = 'mail_message_pre_process_params';

  /**
   * Mail handler sendMailMessage post process params.
   *
   * @Event
   *
   * @var string
   */
  const MAIL_MESSAGE_POST_PROCESS_PARAMS = 'mail_message_post_process_params';

}
