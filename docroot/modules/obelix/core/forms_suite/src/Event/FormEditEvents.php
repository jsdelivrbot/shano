<?php

namespace Drupal\forms_suite\Event;

final class FormEditEvents{

  /**
   * Form edit build process.
   *
   * @Event
   *
   * @var string
   */
  const FORM_EDIT_BUILD = 'form_edit_build';

  /**
   * Form edit validate process.
   *
   * @Event
   *
   * @var string
   */
  const FORM_EDIT_VALIDATE = 'form_edit_validate';

  /**
   * Form edit save process.
   *
   * @Event
   *
   * @var string
   */
  const FORM_EDIT_SAVE = 'form_edit_save';

}
