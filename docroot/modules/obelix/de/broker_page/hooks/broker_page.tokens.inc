<?php

use Drupal\broker_page\Plugin\token\ParentGroupPathAliasToken;

/**
 * Implements hook_token_info()
 */
function broker_page_token_info() {
  $type = array(
    'name' => t('Nodes'),
    'description' => t('Tokens related to individual nodes.'),
    'needs-data' => 'node',
  );

  $node['parent_group_path_alias'] = array(
    'name' => t("Parent group path alias"),
    'description' => t("The parent group path alias of this node (if any)."),
    'type' => 'group',
  );

  return array(
    'types' => array('node' => $type),
    'tokens' => array('node' => $node)
  );
}

/**
 * Implements hook_tokens()
 */
function broker_page_tokens($type, $tokens, array $data, array $options, \Drupal\Core\Render\BubbleableMetadata $bubbleable_metadata) {

  $replacements = array();

  if ($type == 'node' && !empty($data['node'])) {
    /** @var \Drupal\node\NodeInterface $node */
    $node = $data['node'];

    foreach ($tokens as $name => $original) {
      switch ($name) {

        case 'parent_group_path_alias':
          $path_alias = (new ParentGroupPathAliasToken($node))->getPathAlias();
          $replacements[$original] = $path_alias;
          break;
      }
    }

  }
  return $replacements;
}