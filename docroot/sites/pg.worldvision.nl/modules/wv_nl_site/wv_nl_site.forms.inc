<?php

/**
 * @file
 * Forms related logic. NL has many differences so we need to alter some forms etc.
 */

use Drupal\Core\Form\FormStateInterface;

/**
 * Implements hook_form_giftshop_gift_select_form_alter().
 */
function wv_nl_site_form_giftshop_gift_select_form_alter(&$form, FormStateInterface $formState, $form_id) {
  $form['actions']['submit']['#submit'][] = 'wv_nl_site_form_giftshop_gift_select_form_submit';
}

/**
 * Alter submit to change redirect.
 */
function wv_nl_site_form_giftshop_gift_select_form_submit(array &$form, FormStateInterface $form_state) {

  if ($id = $form_state->getValue('id')) {

    \Drupal::service('giftshop.cart.temp')
      ->reset()
      ->setNodeId($id)
      ->setQuantity($form_state->getValue('quantity', 1));

    $form_state->setRedirect('giftshop.cart.add.cert');
  }
}

/**
 * Implements hook_form_giftshop_gift_certificate_form_select_alter().
 */
function wv_nl_site_form_giftshop_gift_certificate_form_alter(&$form, FormStateInterface $formState, $form_id) {
  // NL site has no any other send options.
  if (isset($form['send_type']['#options']['post'])) {
    unset($form['send_type']['#options']['post']);

    $form['send_type']['#default_value'] = 'email';
  }
}
