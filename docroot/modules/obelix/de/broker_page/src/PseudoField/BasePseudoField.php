<?php

namespace Drupal\broker_page\PseudoField;

use Drupal\Core\Entity\EntityInterface;

/**
 * Class BasePseudoField
 */
class BasePseudoField {

  /**
   * @var EntityInterface $entity
   */
  protected $entity;

  /**
   * Constructor.
   *
   * @param EntityInterface $entity
   */
  public function __construct(EntityInterface $entity) {
    $this->entity = $entity;
  }

}
