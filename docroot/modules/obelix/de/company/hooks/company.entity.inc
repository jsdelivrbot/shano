<?php

use Drupal\company\PseudoField\CompanyGiftsField;
use Drupal\company\PseudoField\CompanyHeaderField;
use Drupal\company\PseudoField\CompanyInformationField;
use Drupal\Core\Entity\Display\EntityViewDisplayInterface;
use Drupal\Core\Entity\EntityInterface;

/**
 * Implements hook_entity_view().
 */
function company_entity_view(array &$build, EntityInterface $entity, EntityViewDisplayInterface $display, $view_mode) {
  if (!($entity->getEntityTypeId() == 'group' && $entity->bundle() == 'company')) {
    return;
  }

  $affiliateItem = reset($entity->field_affiliate->referencedEntities());
  if ($affiliateItem) {
    // Set affiliate id cookie until the browser is closed.
    setcookie('refid', $affiliateItem->id(), 0, '/');
  }

  $user_private_store = \Drupal::service('user.private_tempstore')->get('basket_redirect');
  $user_private_store->set('entity_path', 'group/' . $entity->id());

  // remove this page caching because we need retrieve donations count.
  $build['#cache']['max-age'] = 0;

  if($display->getComponent('company_header')) {
    $build['company_header'] = (new CompanyHeaderField($entity))->output();
  }

  if($display->getComponent('company_information')) {
    $build['company_information'] = (new CompanyInformationField($entity))->output();
  }

  if($display->getComponent('company_gifts')) {
    $build['company_gifts'] = (new CompanyGiftsField($entity))->output();
  }
}

/**
 * Implements hook_entity_extra_field_info().
 */
function company_entity_extra_field_info() {
  $fields = array();

  $fields['group']['company']['display']['company_header'] = array(
    'label' => t('Company page header.'),
    'weight' => 100,
    'visible' => TRUE,
  );

  $fields['group']['company']['display']['company_information'] = array(
    'label' => t('Company page information.'),
    'weight' => 100,
    'visible' => TRUE,
  );

  $fields['group']['company']['display']['company_gifts'] = array(
    'label' => t('Company page gifts.'),
    'weight' => 100,
    'visible' => TRUE,
  );

  return $fields;
}