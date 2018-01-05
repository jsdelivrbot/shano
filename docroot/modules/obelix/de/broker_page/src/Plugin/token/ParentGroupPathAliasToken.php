<?php

/**
 * @file
 * Definition of Drupal\broker_page\Plugin\token\ParentGroupPathAliasToken
 */

namespace Drupal\broker_page\Plugin\token;

use Drupal\group\Entity\GroupContent;

/*
 * class ParentGroupPathAliasToken.
 */
class ParentGroupPathAliasToken {

  protected $node;

  public function __construct($node) {
    $this->node = $node;
  }

  /**
   * Get the path alias.
   *
   * @return string
   *   The path alias.
   */
  public function getPathAlias() {
    $group = $this->getParentGroupFromNode($this->node);
    if ($group) {
      $path = '/group/' . $group->id();
      $path_alias = \Drupal::service('path.alias_manager')->getAliasByPath($path, $group->langcode->value);
      return $path_alias;
    } else {
      return FALSE;
    }
  }

  /**
   * Get parent group from node.
   *
   * @param \Drupal\node\Entity\Node
   *   The node entity.
   *
   * @return \Drupal\group\Entity\Group
   *   The group entity.
   */
  private function getParentGroupFromNode($node) {
    $group_content = GroupContent::loadByEntity($node);
    $group_content = reset($group_content);
    if ($group_content) {
      return $group_content->getGroup();
    } else {
      return FALSE;
    }
  }

}