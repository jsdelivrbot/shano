<?php

namespace Drupal\editorial\Plugin\Search;

use Drupal\Core\Database\StatementInterface;
use Drupal\Core\Database\Query\Condition;

/**
 * Handles searching for node entities using the Search module index.
 */
class NodeSearch extends \Drupal\node\Plugin\Search\NodeSearch implements CountableSearchInterface {

  /**
   * {@inheritdoc}
   */
  public function getResultCount() {
    $keys = $this->keywords;

    // Build matching conditions.
    $query = $this->database
      ->select('search_index', 'i', array('target' => 'replica'))
      ->extend('Drupal\search\SearchQuery')
      ->extend('Drupal\Core\Database\Query\PagerSelectExtender');
    $query->join('node_field_data', 'n', 'n.nid = i.sid AND n.langcode = i.langcode');
    $query->condition('n.status', 1)
      ->addTag('node_access')
      ->searchExpression($keys, $this->getPluginId());

    // Handle advanced search filters in the f query string.
    // \Drupal::request()->query->get('f') is an array that looks like this in
    // the URL: ?f[]=type:page&f[]=term:27&f[]=term:13&f[]=langcode:en
    // So $parameters['f'] looks like:
    // array('type:page', 'term:27', 'term:13', 'langcode:en');
    // We need to parse this out into query conditions, some of which go into
    // the keywords string, and some of which are separate conditions.
    $parameters = $this->getParameters();
    if (!empty($parameters['f']) && is_array($parameters['f'])) {
      $filters = array();
      // Match any query value that is an expected option and a value
      // separated by ':' like 'term:27'.
      $pattern = '/^(' . implode('|', array_keys($this->advanced)) . '):([^ ]*)/i';
      foreach ($parameters['f'] as $item) {
        if (preg_match($pattern, $item, $m)) {
          // Use the matched value as the array key to eliminate duplicates.
          $filters[$m[1]][$m[2]] = $m[2];
        }
      }

      // Now turn these into query conditions. This assumes that everything in
      // $filters is a known type of advanced search.
      foreach ($filters as $option => $matched) {
        $info = $this->advanced[$option];
        // Insert additional conditions. By default, all use the OR operator.
        $operator = empty($info['operator']) ? 'OR' : $info['operator'];
        $where = new Condition($operator);
        foreach ($matched as $value) {
          $where->condition($info['column'], $value);
        }
        $query->condition($where);
        if (!empty($info['join'])) {
          $query->join($info['join']['table'], $info['join']['alias'], $info['join']['condition']);
        }
      }
    }

    // Add the ranking expressions.
    $this->addNodeRankings($query);

    // Get result count.
    return $query->countQuery()
      ->execute()
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  protected function prepareResults(StatementInterface $found) {
    $results = parent::prepareResults($found);

    foreach ($results as $delta => $result) {
      if ($tags = \Drupal::service('metatag.manager')
        ->tagsFromEntity($result['node'])
      ) {
        if (isset($tags['title'])) {
          $results[$delta]['title'] = \Drupal::token()->replace($tags['title']);
        }
      }
    }

    return $results;
  }
}
