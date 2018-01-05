<?php

/**
 * Implements hook_theme().
 */
function broker_page_theme() {
  $theme = [];

  $theme['broker_page'] = array(
    'template' => 'broker-page',
    'variables' => [
      'content' => NULL,
    ],
  );

  $theme['broker_page_header'] = array(
    'template' => 'broker-page--header',
    'variables' => [
      'content' => NULL,
    ],
  );

  $theme['broker_page_information'] = array(
    'template' => 'broker-page--information',
    'variables' => [
      'left' => NULL,
      'center' => NULL,
      'right' => NULL,
    ],
  );

  $theme['broker_page_gifts'] = array(
    'template' => 'broker-page--gifts',
    'variables' => [
      'gifts' => NULL,
    ],
  );

  return $theme;
}