<?php

/**
 * @file
 * Contains \Drupal\editorial_content\EditorialContentListBuilder.
 */

namespace Drupal\editorial_content;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Routing\LinkGeneratorTrait;
use Drupal\Core\Url;

/**
 * Defines a class to build a listing of Editorial content entities.
 *
 * @ingroup editorial_content
 */
class EditorialContentListBuilder extends EntityListBuilder {
  use LinkGeneratorTrait;
  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('ID');
    $header['type'] = $this->t('Type');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $editorial_content_type_storage = \Drupal::entityTypeManager()->getStorage('editorial_content_type');
    $editorial_content_type = $editorial_content_type_storage->load($entity->bundle())->label();

    /* @var $entity \Drupal\editorial_content\Entity\EditorialContent */
    $row['id'] = $entity->id();
    $row['type'] = $editorial_content_type;
    $row['name'] = $this->l(
      $entity->label(),
      new Url(
        'entity.editorial_content.canonical', array(
          'editorial_content' => $entity->id(),
        )
      )
    );
    return $row + parent::buildRow($entity);
  }

}
