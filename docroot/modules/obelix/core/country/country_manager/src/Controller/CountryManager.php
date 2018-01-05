<?php

namespace Drupal\country_manager\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\country\Entity\Country;
use Drupal\datasourcehandler\Datasource;
use Drupal\datasourcehandler\DatasourceService;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class CountryManager.
 *
 * @package Drupal\country_manager\Controller
 */
class CountryManager extends ControllerBase {

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
  public function __construct($datasource)
  {
    $this->datasource = $datasource->get('default');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container)
  {
    return new static(
        $container->get('datasource')
    );
  }

  /**
   * Create Country.
   *
   * @param $country_code
   * @param $country_name
   * @return Country
   * Return Hello string.
   */
  public function createCountry($country_code, $country_name) {

    $country_entity_type = $this->entityTypeManager()->getStorage('country');
    $country = $country_entity_type->load($country_code);

    if ($country === NULL) {
      $country = $country_entity_type->create([
          'id' => $country_code,
          'name' => $country_name,
      ]);
    }
    $country->save();

    return $country;
  }

}
