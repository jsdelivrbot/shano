<?php

/**
 * @file
 * Contains \Drupal\forms_suite\FormListBuilder.
 */

namespace Drupal\forms_suite;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;

/**
 * Defines a class to build a listing of forms form entities.
 *
 * @see \Drupal\forms_suite\Entity\Form
 */
class FormListBuilder extends ConfigEntityListBuilder
{
  /**
   * {@inheritdoc}
   */
  protected function getEntityIds() {
    $label = \Drupal::request()->get('label');
    $query = $this->getStorage()->getQuery()
      ->sort($this->entityType->getKey('id'));
    if ($label) {
      $query->condition('label', $label, '=');
    }

    if ($this->limit) {
      $query->pager($this->limit);
    }
    return $query->execute();
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader()
  {
    $header['form'] = t('Form');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity)
  {
    if (!empty($entity->getAlias())) {
      $url = Url::fromUserInput($entity->getAlias());
      $row['form'] = Link::fromTextAndUrl($entity->label(), $url);
    } else {
      $row['form'] = $entity->link(NULL, 'canonical');
    }
    return $row + parent::buildRow($entity);
  }

  public function getDefaultOperations(EntityInterface $entity)
  {
    $operations = parent::getDefaultOperations($entity);
    $url = Url::fromRoute('forms_suite.form.copy', ['uuid' => $entity->get('uuid')]);

    $operations['copy'] = [
      'title' => t('Copy'),
      'weight' => 10,
      'url' => $url,
    ];

    return $operations;
  }

}
