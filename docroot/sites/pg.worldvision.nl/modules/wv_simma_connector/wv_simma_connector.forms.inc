<?php

/**
 * @file
 * Forms related logic.
 */

use Drupal\Core\Form\FormStateInterface;

/**
 * Implements hook_form_alter().
 */
function wv_simma_connector_form_alter(&$form, FormStateInterface $form_state, $form_id) {
  $route = \Drupal::routeMatch()->getRouteObject();

  // Avoid errors on admin pages when you adding sql export field on form suite.
  $is_admin = \Drupal::service('router.admin_context')->isAdminRoute($route);

  // Hide payment method checkbox.
  if (!empty($form['field_payment_method']['widget']['paymentMethod']['#type'])) {
    $form['field_payment_method']['widget']['paymentMethod']['#attributes']['class'][] = 'hidden';
  }

  if (!$is_admin && !empty($form['field_form_sql_export'])) {
    // Hide this field by default.
    $form['field_form_sql_export']['widget']['value']['#type'] = 'hidden';
    $form['field_form_sql_export']['widget']['value']['#value'] = TRUE;

    // Sometimes actions have no submits, so if we fill it then main submit will be ignored.
    if (!empty($form['actions']['submit']['#submit'])) {
      $form['actions']['submit']['#submit'][] = 'wv_simma_connector_form_export_submit';
    }
    else {
      $form['#submit'][] = 'wv_simma_connector_form_export_submit';
    }
  }

  // Alter NL fields.
  if (!empty($form['field_user_data']['widget']['countryCode'])) {
    $form['field_user_data']['widget']['countryCode']['#default_value'] = 'NL';
  }

  switch ($form_id) {
    case 'forms_message_sponsor_form':
      $hidden_widgets = [
        'field_yearly_donation' => [
          'month13', 'month13_check'
        ],
      ];

      foreach ($hidden_widgets as $field_name => $columns) {
        foreach ($columns as $column) {
          if (!empty($form[$field_name]['widget'][$column]['#type'])) {
            $form[$field_name]['widget'][$column]['#type'] = 'hidden';
          }
        }
      }

      break;
  }

}

/**
 * Form export submit. Queues sql export jobs.
 */
function wv_simma_connector_form_export_submit(array &$form, FormStateInterface $form_state) {

  if ($form_state->isMethodType('GET')) {
    return;
  }

  $form_obj = $form_state->getFormObject();

  // Queue only forms with entity context.
  if ($form_obj && method_exists($form_obj, 'getEntity') && ($entity = $form_obj->getEntity())) {
    \Drupal::service('wv_simma_connector.entity_sql_export')->queue($entity, $form['#form_id']);
  }
}
