<?php

namespace Drupal\affiliate\Entity;

use Drupal\views\EntityViewsData;
use Drupal\views\EntityViewsDataInterface;

/**
 * Provides Views data for Affiliate entities.
 */
class AffiliateItemViewsData extends EntityViewsData implements EntityViewsDataInterface {
  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    $data['affiliate']['table']['base'] = array(
      'field' => 'id',
      'title' => $this->t('Affiliate'),
      'help' => $this->t('The Affiliate ID.'),
    );

    return $data;
  }

}
