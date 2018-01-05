<?php

namespace Drupal\affiliate;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Routing\LinkGeneratorTrait;
use Drupal\Core\Url;

/**
 * Defines a class to build a listing of Affiliate entities.
 *
 * @ingroup affiliate
 */
class AffiliateItemListBuilder extends EntityListBuilder {
  use LinkGeneratorTrait;
  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Reference ID');
    $header['name'] = $this->t('Partner name');
    $header['type'] = $this->t('Type');
    $header['motivation_code'] = $this->t('Motivation code');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\affiliate\Entity\AffiliateItem */
    $row['id'] = $entity->id();
    $row['name'] = $this->l(
      $entity->label(),
      new Url(
        'entity.affiliate.edit_form', array(
          'affiliate' => $entity->id(),
        )
      )
    );
    $row['type'] = $entity->bundle();
    $row['motivation_code'] = $entity->getMotivationCode();
    return $row + parent::buildRow($entity);
  }

}
