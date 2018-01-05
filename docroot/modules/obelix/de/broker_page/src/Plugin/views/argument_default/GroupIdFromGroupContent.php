<?php

namespace Drupal\broker_page\Plugin\views\argument_default;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Cache\CacheableDependencyInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Url;
use Drupal\group\Entity\GroupContent;
use Drupal\views\Plugin\views\argument_default\ArgumentDefaultPluginBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Default argument plugin to extract a group ID.
 *
 * @ViewsArgumentDefault(
 *   id = "group_id_from_group_content",
 *   title = @Translation("Group ID from Group Content (node)")
 * )
 */
class GroupIdFromGroupContent extends ArgumentDefaultPluginBase implements CacheableDependencyInterface {

  /**
   * The route match.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $node;

  /**
   * Constructs a new Node instance.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, RouteMatchInterface $route_match) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $current_url = Url::fromRoute('<current>');
    $path = $current_url->toString();

    if(preg_match('/node\/(\d+)/', $path, $matches)) {
      $this->node = \Drupal\node\Entity\Node::load($matches[1]);
    }

  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_route_match')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getArgument() {
    if ($this->node) {
      $group = $this->getParentGroupFromNode($this->node);
      if ($group) {
        return $group->id();
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge() {
    return Cache::PERMANENT;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    return ['url'];
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
