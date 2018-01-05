<?php

namespace Drupal\broker_page\PseudoField;

use Drupal\Core\Link;
use Drupal\Core\Url;

/**
 * Class UserBrokerPageInformationField
 */
class UserBrokerPageInformationField extends BasePseudoField {

  /**
   * Display output for the pseudo field.
   *
   * @return array
   */
  public function output() {

    $broker_pages = $this->getBrokerPage($this->entity);
    $links = [];

    foreach ($broker_pages as $broker_page) {
      $url = Url::fromRoute('entity.node.edit_form', array('node' => $broker_page->id()));
      $project_link = Link::fromTextAndUrl(t('Your broker page'), $url);
      $project_link = $project_link->toRenderable();
      $links[] = $project_link;
    }

    return $links;
  }

  /**
   * Get the broker page from user.
   *
   * @param \Drupal\entity\user
   *   The user entity.
   *
   * @return \Drupal\node\Entity\Node
   *   Broker page entity.
   */
  private function getBrokerPage($user) {
    $query = \Drupal::database()->select('node_field_data', 'n');
    $query->fields('n', ['nid']);
    $query->condition('n.type', 'broker_page');
    $query->condition('n.uid', $user->uid->value);

    $query_results = $query->execute()->fetchAll();
    $broker_page_ids = [];
    foreach ($query_results as $item) {
      $broker_page_ids[] = $item->nid;
    }

    return \Drupal::entityTypeManager()->getStorage('node')->loadMultiple($broker_page_ids);
  }

}
