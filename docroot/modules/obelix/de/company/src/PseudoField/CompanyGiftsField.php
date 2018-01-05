<?php

namespace Drupal\company\PseudoField;

use Drupal\Core\Url;

/**
 * Class CompanyGiftsField
 */
class CompanyGiftsField extends BasePseudoField {

  /**
   * Display output for the pseudo field.
   *
   * @return array
   */
  public function output() {
    $company = $this->entity;
    $gifts = [];

    if (!$company->field_relation_teaser->isEmpty()) {
      $relation_teaser_entities = $company->field_relation_teaser->referencedEntities();
      foreach ($relation_teaser_entities as $relation_teaser_entity) {
        $view_builder = \Drupal::entityTypeManager()->getViewBuilder('editorial_content');
        $gifts[] = render($view_builder->view($relation_teaser_entity, 'full'));
      }
    }

    if ($gifts) {
      return [
        '#theme' => 'company_page_gifts',
        '#attributes' => [],
        '#gifts' => $gifts,
      ];
    }
  }

}
