<?php

/**
 * @file
 * Drush commands for Giftshop modules.
 */

/**
 * Implements hook_drush_help().
 */
function giftshop_drush_help($section) {
  switch ($section) {
    case 'meta:giftshop:title':
      return dt('Giftshop commands');
    case 'meta:giftshop:summary':
      return dt('Interact with Giftshop module.');
  }
}

/**
 * Implements hook_drush_command().
 */
function giftshop_drush_command() {
  $items = [];
  $items['giftshop-export-xlsx'] = [
    'description' => dt('Exports a range of orders to xlsx format.'),
    'examples' => [
      'Standard example' => 'drush giftshop-export-xlsx',
    ],
    'arguments' => array(
      'from' => 'A unixtimestamp which defines the start point for the export.',
      'to' => 'A unixtimestamp which defines the end point for the export. (Default: Now)',
    ),
    'aliases' => ['giftshop-xlsx'],
  ];
  return $items;
}

/**
 * Callback function for drush giftshop-export-xlsx.
 *
 * Callback is called by using drush_hook_command() where
 * hook is the name of the module (giftshop) and command is the name of
 * the Drush command with all "-" characters converted to "_" characters.
 */
function drush_giftshop_export_xlsx($from, $to = REQUEST_TIME) {
  print \Drupal::service('giftshop.orders')->exportXLSX($from, $to);
}
