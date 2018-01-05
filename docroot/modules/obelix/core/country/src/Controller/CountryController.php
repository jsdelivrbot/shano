<?php

namespace Drupal\country\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;
use Drupal\country\Entity\Country;

/**
 * Class CountryController.
 *
 * @package Drupal\child\Controller
 */
class CountryController extends ControllerBase
{
  /**
   * Returns the Country list as assoc array.
   *
   * @return array
   *  Country assoc array.
   */
  public static function getCountryOptions()
  {
    $db = Database::getConnection();
    $results = $db->select('country')
      ->fields('country', ['id', 'name'])->distinct();
    $countries = $results->execute()->fetchAllKeyed(0, 0);

    // @todo please set up a mapping function for all iVision fields

    $country_storage = \Drupal::entityTypeManager()->getStorage('country');
    $countries = $country_storage->loadMultiple($countries);

    return self::buildSortedCountryList($countries);
  }

  /**
   * Returns the Country list of Country's with free children as assoc array.
   *
   * @return array
   *  Country assoc array.
   */
  public static function getCountryOptionsWithChildren()
  {
    $db = Database::getConnection();
    $results = $db->select('country')
      ->fields('country', ['id'])->distinct();
    $results->join('project__field_country', 'p_ref', 'p_ref.field_country_target_id = country.id');
    $results->join('project', 'p', 'p_ref.entity_id = p.project_id');
    $results->join('child__field_child_project', 'c_ref', 'c_ref.field_child_project_target_id = p_ref.entity_id');
    $results->join('child', 'c', 'c.ivision_id = c_ref.entity_id');

    $countries = $results->execute()->fetchAllKeyed(0, 0);

    // @todo please set up a mapping function for all iVision fields

    $country_storage = \Drupal::entityTypeManager()->getStorage('country');
    $countries = $country_storage->loadMultiple($countries);

    return self::buildSortedCountryList($countries);
  }

  /**
   * Returns a static Country list of Country's as assoc array.
   *
   * @return array
   *  Country assoc array.
   */
  public static function getStaticCountryOptions()
  {

    // Restrict countries by active projects.
    if (\Drupal::config('wv_site.settings')->get('restrict_countries_by_active_projects')) {
      $cid = 'allowed-countries';

      if ($cache = \Drupal::cache('wv_country')->get($cid)) {
        $countries = $cache->data;
      }
      else {
        $project_storage = \Drupal::entityTypeManager()->getStorage('project');
        $active_projects = $project_storage->loadByProperties(['status' => 1]);

        $countries = [];

        foreach ($active_projects as $project) {
          if ($country = $project->field_country->target_id) {
            $countries[$country] = $country;
          }
        }

        $countries = array_values($countries);

        // If we have no countries don't cache it as it's small overhead without entity load.
        // So we load countries as soon as at least one project is added.
        if ($countries) {
          \Drupal::cache('wv_country')->set($cid, $countries);
        }
      }

    } else {
      // Keep DE logic as it's.
      $countries = [
        'BGD',
        'CAM',
        'IND',
        'IDN',
        'MOG',
        'MYA',
        'LKA',
        'VNM',
        'ETH',
        'BDI',
        'GHA',
        'KEN',
        'MLI',
        'MRT',
        'SEN',
        'SIE',
        'ZIM',
        'SWZ',
        'TZA',
        'TCD',
        'BOL',
        'DOM',
        'GTM',
        'HND',
        'NIC',
        'PER'
      ];
    }

    $country_storage = \Drupal::entityTypeManager()->getStorage('country');
    $countries = $country_storage->loadMultiple($countries);

    return self::buildSortedCountryList($countries);
  }

  /**
   * Returns a sorted assoc list of sorted country's nested by continents.
   *
   * @param $countries
   *  list of country's
   * @return array
   *  list of sorted country's nested by continents
   */
  private static function buildSortedCountryList($countries)
  {
    $countries_sorted = [];

    // build nested continents
    foreach ($countries as $country) {
      /** @var Country $country */
      if ($continent = $country->getContinent()) {
        $countries_sorted[$continent][$country->getCountryCode()] = $country->getName();
      } else {
        $countries_sorted[$country->getCountryCode()] = $country->getName();
      }
    }
    // sort country list for continents
    foreach ($countries_sorted as &$sub_region) {
      if (is_array($sub_region)) {
        asort($sub_region);
      }
    }

    ksort($countries_sorted);
    return $countries_sorted;
  }

}
