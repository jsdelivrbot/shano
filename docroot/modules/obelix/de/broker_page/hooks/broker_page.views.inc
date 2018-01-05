<?php

/**
 * Implements hook_views_data_alter().
 */
function broker_page_views_data_alter(array &$data) {
  $data['node']['donation_count'] = array(
    'title' => t('Donation count'),
    'field' => array(
      'title' => t('Donation count'),
      'help' => t('Shows the count of donations for one broker.'),
      'id' => 'donation_count',
    ),
  );
}
