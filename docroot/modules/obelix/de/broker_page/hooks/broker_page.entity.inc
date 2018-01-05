<?php

use Drupal\broker_page\PseudoField\BrokerPageGiftsField;
use Drupal\broker_page\PseudoField\BrokerPageHeaderField;
use Drupal\broker_page\PseudoField\BrokerPageInformationField;
use Drupal\broker_page\PseudoField\UserBrokerPageInformationField;
use Drupal\Core\Entity\Display\EntityViewDisplayInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\group\Entity\GroupContent;

/**
 * Implements hook_entity_view().
 */
function broker_page_entity_view(array &$build, EntityInterface $entity, EntityViewDisplayInterface $display, $view_mode) {
  if ($entity->getEntityTypeId() == 'node' && $entity->bundle() == 'broker_page') {
    // remove this page caching because we need to set the cookie and retrieve donations count.
    $build['#cache']['max-age'] = 0;

    $affiliateItem = reset($entity->field_affiliate->referencedEntities());
    if ($affiliateItem) {
      // Set affiliate id cookie until the browser is closed.
      setcookie('refid', $affiliateItem->id(), 0, '/');
    }

    $user_private_store = \Drupal::service('user.private_tempstore')->get('basket_redirect');
    $user_private_store->set('entity_path', 'node/' . $entity->id());

    if($display->getComponent('broker_page_header')) {
      $build['broker_page_header'] = (new BrokerPageHeaderField($entity))->output();
    }

    if($display->getComponent('broker_page_information')) {
      $build['broker_page_information'] = (new BrokerPageInformationField($entity))->output();
    }

    if($display->getComponent('broker_page_gifts')) {
      $build['broker_page_gifts'] = (new BrokerPageGiftsField($entity))->output();
    }
  }

  if ($entity->getEntityTypeId() == 'user' && $entity->bundle() == 'user') {
    if($display->getComponent('user_broker_page_information')) {
      $build['user_broker_page_information'] = (new UserBrokerPageInformationField($entity))->output();
    }
  }
}

/**
 * Implements hook_entity_extra_field_info().
 */
function broker_page_entity_extra_field_info() {
  $fields = array();

  $fields['node']['broker_page']['display']['broker_page_header'] = array(
    'label' => t('Broker page header.'),
    'weight' => 100,
    'visible' => TRUE,
  );

  $fields['node']['broker_page']['display']['broker_page_information'] = array(
    'label' => t('Broker page information.'),
    'weight' => 100,
    'visible' => TRUE,
  );

  $fields['node']['broker_page']['display']['broker_page_gifts'] = array(
    'label' => t('Broker page gifts.'),
    'weight' => 100,
    'visible' => TRUE,
  );

  $fields['user']['user']['display']['user_broker_page_information'] = array(
    'label' => t('User broker page information.'),
    'weight' => 100,
    'visible' => TRUE,
  );

  return $fields;
}