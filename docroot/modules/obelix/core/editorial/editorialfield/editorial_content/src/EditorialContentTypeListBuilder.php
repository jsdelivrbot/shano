<?php

/**
 * @file
 * Contains \Drupal\editorial_content\EditorialContentTypeListBuilder.
 */

namespace Drupal\editorial_content;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;

/**
 * Provides a listing of editorial content type entities.
 */
class EditorialContentTypeListBuilder extends ConfigEntityListBuilder {
  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = $this->t('Name');
    $header['id'] = $this->t('Machine name');
    $header['category'] = $this->t('Category');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row['label'] = $entity->label();
    $row['id'] = $entity->id();
    $row['category'] = $entity->getCategory();
    // You probably want a few more properties here...
    return $row + parent::buildRow($entity);
  }

}
