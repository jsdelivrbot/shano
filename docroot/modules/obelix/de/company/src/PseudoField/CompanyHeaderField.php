<?php

namespace Drupal\company\PseudoField;

/**
 * Class CompanyHeaderField
 */
class CompanyHeaderField extends BasePseudoField {

  /**
   * Display output for the pseudo field.
   *
   * @return array
   */
  public function output() {
    $company = $this->entity;
    if (!$company->field_campaign_teaser->isEmpty()) {
      $view_builder = \Drupal::entityTypeManager()->getViewBuilder('editorial_content');
      $content = render($view_builder->view($company->field_campaign_teaser->referencedEntities()[0], 'full'));
    }

    if ($content) {
      return [
        '#theme' => 'company_page_header',
        '#attributes' => [],
        '#content' => $content,
      ];
    }
  }

}
