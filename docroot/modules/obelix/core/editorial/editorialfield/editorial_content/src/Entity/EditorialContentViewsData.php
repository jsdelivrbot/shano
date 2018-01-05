<?php

/**
 * @file
 * Contains \Drupal\editorial_content\Entity\EditorialContent.
 */

namespace Drupal\editorial_content\Entity;

use Drupal\views\EntityViewsData;
use Drupal\views\EntityViewsDataInterface;

/**
 * Provides Views data for Editorial content entities.
 */
class EditorialContentViewsData extends EntityViewsData implements EntityViewsDataInterface {
  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    $data['editorial_content']['table']['base'] = array(
      'field' => 'id',
      'title' => $this->t('Editorial content'),
      'help' => $this->t('The Editorial content ID.'),
    );

    return $data;
  }

}
