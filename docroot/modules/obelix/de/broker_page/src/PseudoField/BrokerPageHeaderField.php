<?php

namespace Drupal\broker_page\PseudoField;


use Drupal\group\Entity\GroupContent;

/**
 * Class BrokerPageHeaderField
 */
class BrokerPageHeaderField extends BasePseudoField {

  /**
   * Display output for the pseudo field.
   *
   * @return array
   */
  public function output() {
    $broker_page = $this->entity;
    $group_content = GroupContent::loadByEntity($broker_page);
    $group_content = reset($group_content);
    if ($group_content) {
      $company = $group_content->getGroup();

      if (!$company->field_broker_teaser->isEmpty()) {
        $view_builder = \Drupal::entityTypeManager()->getViewBuilder('editorial_content');
        $content = render($view_builder->view($company->field_broker_teaser->referencedEntities()[0], 'full'));
      }

      if ($content) {
        return [
          '#theme' => 'broker_page_header',
          '#attributes' => [],
          '#content' => $content,
        ];
      }
    }
  }

}
