<?php

namespace Drupal\broker_page\PseudoField;

use Drupal\Core\Url;

/**
 * Class CompanyGiftsField
 */
class BrokerPageGiftsField extends BasePseudoField {

  /**
   * Display output for the pseudo field.
   *
   * @return array
   */
  public function output() {
    $broker_page = $this->entity;
    $gifts = [];

    if (!$broker_page->field_relation_teaser->isEmpty()) {
      $relation_teaser_entities = $broker_page->field_relation_teaser->referencedEntities();
      foreach ($relation_teaser_entities as $relation_teaser_entity) {
        $view_builder = \Drupal::entityTypeManager()->getViewBuilder('editorial_content');
        $gifts[] = render($view_builder->view($relation_teaser_entity, 'full'));
      }
    }

    if ($gifts) {
      return [
        '#theme' => 'broker_page_gifts',
        '#attributes' => [],
        '#gifts' => $gifts,
      ];
    }
  }

}
