<?php

/**
 * Implements hook_mail().
 */
function company_mail($key, &$message, $params) {
  $options = array(
    'langcode' => $message['langcode'],
  );

  switch ($key) {
    case 'company_broker_subscribe':
      $message['headers']['Content-Type'] = 'text/html; charset=UTF-8; format=flowed; delsp=yes';
      $message['from'] = \Drupal::config('system.site')->get('mail');
      $message['subject'] = t('New broker request: @title', array('@title' => $params['broker_name']), $options);
      $message['body'][] = $params['message'];
      break;
    case 'message_broker_created_confirmation':
      $message['headers']['Content-Type'] = 'text/html; charset=UTF-8; format=flowed; delsp=yes';
      $message['from'] = \Drupal::config('system.site')->get('mail');
      $message['subject'] = t('Your broker page "@title" has been created.', array('@title' => $params['broker_page_title']), $options);
      $message['body'][] = $params['message'];
      break;
  }
}