<?php

namespace Drupal\map\Entity;

use Drupal\views\EntityViewsData;
use Drupal\views\EntityViewsDataInterface;

/**
 * Provides Views data for Map entities.
 */
class MapViewsData extends EntityViewsData implements EntityViewsDataInterface {
  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    $data['map']['table']['base'] = array(
      'field' => 'id',
      'title' => $this->t('Map'),
      'help' => $this->t('The Map ID.'),
    );

    return $data;
  }

}
