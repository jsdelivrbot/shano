<?php

namespace Drupal\country\Entity;

use Drupal\views\EntityViewsData;
use Drupal\views\EntityViewsDataInterface;

/**
 * Provides Views data for Country entities.
 */
class CountryViewsData extends EntityViewsData implements EntityViewsDataInterface {
  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    $data['country']['table']['base'] = array(
      'field' => 'id',
      'title' => $this->t('Country'),
      'help' => $this->t('The Country ID.'),
    );

    return $data;
  }

}
