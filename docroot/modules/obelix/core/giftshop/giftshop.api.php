<?php

/**
 * @file
 * Module defined hooks API.
 */

use Drupal\Core\Url;

/**
 * @param array $build
 *  Renderable array.
 */
function hook_cart_build_alter(array &$build) {
  $build['#actions']['checkout']['#url'] = Url::fromUri('internal:/forms/gift');
}

/**
 * Used to handle checkout gifts, as multiple gifts are split & saved to cloned
 * forms_message entity, each message can have only one gift.
 *
 * @param $message
 *  Forms message entity with just checkout gift.
 * @param $form_id
 */
function hook_giftshop_gift_checkout($message, $form_id) {
  // Do something with message.
}
