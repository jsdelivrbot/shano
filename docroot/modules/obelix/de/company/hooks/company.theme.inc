<?php

/**
 * Implements hook_theme().
 */
function company_theme() {
  $theme = [];

  $theme['company_subscribe_form_page'] = array(
    'template' => 'company-subscribe-form-page',
    'variables' => [
      'header' => NULL,
      'introduction' => NULL,
      'form' => NULL,
    ],
  );

  $theme['company_subscribe_form'] = array(
    'template' => 'company-subscribe-form',
    'render element' => 'form',
  );

  $theme['company_subscribe_form_confirmation'] = array(
    'template' => 'company-subscribe-form-confirmation',
    'variables' => [
      'header' => NULL,
      'message' => NULL,
    ],
  );

  $theme['company_page'] = array(
    'template' => 'company-page',
    'variables' => [
      'content' => NULL,
    ],
  );

  $theme['company_page_header'] = array(
    'template' => 'company-page--header',
    'variables' => [
      'content' => NULL,
    ],
  );

  $theme['company_page_information'] = array(
    'template' => 'company-page--information',
    'variables' => [
      'left' => NULL,
      'right' => NULL,
    ],
  );

  $theme['company_page_gifts'] = array(
    'template' => 'company-page--gifts',
    'variables' => [
      'gifts' => NULL,
    ],
  );

  return $theme;
}