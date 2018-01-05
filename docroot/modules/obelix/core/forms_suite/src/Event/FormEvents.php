<?php

namespace Drupal\forms_suite\Event;

final class FormEvents {

  /**
   * Forms are submitted with success.
   *
   * @Event
   *
   * @var string
   */
  const SUBMITTED_SUCCESS = 'form_submitted_success';

  /**
   * Forms are submitted with errors.
   *
   * @Event
   *
   * @var string
   */
  const SUBMITTED_ERROR = 'form_submitted_error';

  /**
   * Forms pre save after submit.
   *
   * @Event
   *
   * @var string
   */
  const SUBMITTED_PRE_SAVE = 'form_pre_submitted_error';

  /**
   * Forms post save after submit.
   *
   * @Event
   *
   * @var string
   */
  const SUBMITTED_POST_SAVE = 'form_post_submitted_error';

  /**
   * Forms build.
   *
   * @Event
   *
   * @var string
   */
  const BUILD_PROCESS = 'form_build';

}
