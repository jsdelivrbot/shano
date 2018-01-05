<?php

namespace Drupal\map\Entity;

use Drupal\views\EntityViewsData;
use Drupal\views\EntityViewsDataInterface;

/**
 * Provides Views data for Geo object entities.
 */
class GeoObjectViewsData extends EntityViewsData implements EntityViewsDataInterface {
  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    $data['geo_object']['table']['base'] = array(
      'field' => 'id',
      'title' => $this->t('Geo object'),
      'help' => $this->t('The Geo object ID.'),
    );

    return $data;
  }

}
