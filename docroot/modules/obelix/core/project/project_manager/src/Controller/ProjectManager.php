<?php

namespace Drupal\project_manager\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\country\Entity\Country;
use Drupal\country_manager\Controller\CountryManager;
use Drupal\datasourcehandler\Datasource;
use Drupal\datasourcehandler\DatasourceService;
use Drupal\project\Entity\Project;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\datasourcehandler\DatasourceServiceLocal;

/**
 * Class ProjectManager.
 *
 * @package Drupal\project_manager\Controller
 */
class ProjectManager extends ControllerBase {

  /**
   * Drupal\datasourcehandler\DatasourceService definition.
   *
   * @var Datasource
   */
  protected $datasource;

  /**
   * {@inheritdoc}
   * @param $datasource DatasourceService
   */
  public function __construct($datasource) {
    $this->datasource = $datasource->get('default');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('datasource')
    );
  }

  /**
   * Update all projects.
   *
   * @return string
   *   Return Hello string.
   */
  public function updateProjects() {
    $project_entity_type = $this->entityTypeManager()->getStorage('project');
    $projects = $project_entity_type->loadMultiple();

    foreach ($projects as &$project) {
      $this->datasource->getProjectClass()->updateProjectData($project);
    }

    return [
      '#type' => 'markup',
      '#markup' => $this->t('Implement method: importProjects')
    ];
  }

  /**
   * Create project.
   *
   * @param $project_id
   * @param $country Country
   * @param string $project_name
   * @return Project
   */
  public function createProject($project_id, $country, $project_name = '') {

    $project_entity_type = $this->entityTypeManager()->getStorage('project');
    $project = $project_entity_type->load($project_id);

    if ($project === NULL) {
      $project = $project_entity_type->create([
        'project_id' => $project_id,
        'name' => 'sdsd',
        'field_country' => $country,
      ]);
    }

    $this->datasource->getProjectClass()->updateProjectData($project);
    return $project;
  }
}
