<?php

namespace Drupal\map\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Drupal\country\Entity\Country;
use Drupal\file\Entity\File;
use Drupal\map\Entity\GeoObject;
use Drupal\map\Entity\Map;
use Drupal\map\MapInterface;

/**
 * Class MapController.
 *
 * @package Drupal\map\Controller
 */
class MapController extends ControllerBase
{
  /**
   * Render map.
   *
   * @param $id
   *  Id of the Map.
   * @return array
   */
  public function loadMap($id)
  {


    $map_entity_type = $this->entityTypeManager()->getStorage('map');
    /** @var Map $map */
    $map = $map_entity_type->load($id);
    $map_data = [
      'lat' => $map->get('field_lat')->value,
      'lng' => $map->get('field_lng')->value,
      'zoom' => $map->get('field_zoom')->value,
    ];

//    $view_builder = \Drupal::entityManager()->getViewBuilder('map');
//    $full_output = $view_builder->view($map);
//
//    return $full_output;


    $geo_objects = $map->getGeoObjects();

    return [
      '#theme' => 'world-map',
      '#countrys' => $this->getCountryData($geo_objects),
      '#attached' => [
        'drupalSettings' => [
          'map' => [
            'map_data' => $map_data,
            'geo_json' => $this->generateGeoJson($geo_objects),
          ]
        ],
      ],
    ];
  }

  /**
   * Generates the geo JSON array.
   *
   * @param array $geo_objects
   * @return array
   */
  public function generateGeoJson(array $geo_objects)
  {
    $geo_json = [
      'type' => 'FeatureCollection',
    ];
    $geo_json['features'] = [];
    foreach ($geo_objects as $geo_object) {
      /** @var GeoObject $geo_object */

      $json = json_decode($geo_object->getJson(), TRUE);

      // create geo json array with all existing objects.
      foreach ($json as $object_type => $object_value) {
        if (isset($object_type) && !empty($object_type)) {
          foreach ($object_value as $objects) {
            $feature = [];
            $feature['type'] = 'Feature';

            // list of properties
            $feature['properties']['country'] = $geo_object->getCountryID();
            $feature['properties']['highlighted'] = $geo_object->getHighlighted()->value;

            // switch type geometry
            switch ($object_type) {
              case 'polygons':
                $feature['geometry']['type'] = 'Polygon';
                // create same coordinates pairs at start and end for polygons.
                $objects[] = $objects[0];
                foreach ($objects as $coordinates) {
                  $feature['geometry']['coordinates'][] = [$coordinates['lng'], $coordinates['lat']];
                }
                $feature['geometry']['coordinates'] = [$feature['geometry']['coordinates']];
                break;
              case 'markers':
                $feature['geometry']['type'] = 'Point';
                $feature['geometry']['coordinates'][] = $objects['lng'];
                $feature['geometry']['coordinates'][] = $objects['lat'];
                break;
            }
            $geo_json['features'][] = $feature;
          }
        }
      }

    }
    return $geo_json;
  }

  /**
   * Returns a array with a list of country's.
   * Every country has the necessary data.
   *
   * @param array $geo_objects
   * @return array
   *  List of country data.
   */
  public function getCountryData(array $geo_objects)
  {
    $countrys = [];
    $country_entity_type = $this->entityTypeManager()->getStorage('country');

    foreach ($geo_objects as $geo_object) {
      /** @var GeoObject $geo_object */
      if ($geo_object->getCountryID()) {
        $country_id = $geo_object->getCountryID();
        /** @var Country $country */
        $country = $country_entity_type->load($country_id);

        // render catastrophe link
        $catastrophe_link = $country->get('field_catastrophe_link')->getValue();
        if ($catastrophe_link) {
          $catastrophe_link = [
            'link' => Url::fromUri($catastrophe_link[0]['uri'])->toString(),
            'title' => $catastrophe_link[0]['title'],
          ];
        }

        // render about link
        $about_the_country_link = $country->get('field_about_the_country_link')->getValue();
        if ($about_the_country_link) {
          $about_the_country_link = [
            'link' => Url::fromUri($about_the_country_link[0]['uri'])->toString(),
            'title' => $about_the_country_link[0]['title'],
          ];
        }

        // render success link
        $success_link = $country->get('field_success_link')->getValue();
        if ($success_link) {
          $success_link = [
            'link' => Url::fromUri($success_link[0]['uri'])->toString(),
            'title' => $success_link[0]['title'],
          ];
        }

        // render country image
        $image = $country->get('field_image')->getValue();
        if ($image) {
          $file = File::load($image[0]['target_id']);
          $image = [
            '#theme' => 'image_style',
            '#width' => $image[0]['width'],
            '#alt' => $image[0]['alt'],
            '#height' => $image[0]['height'],
            '#style_name' => 'large',
            '#uri' => $file->getFileUri(),
          ];
        }

        // set all values
        $countrys[$country_id] = [
          'name' => $country->getName(),
          'about_the_country' => $country->get('field_about_the_country')->value,
          'about_the_country_link' => $about_the_country_link,
          'catastrophe_headline' => $country->get('field_catastrophe_headline')->value,
          'catastrophe_link' => $catastrophe_link,
          'catastrophe_text' => $country->get('field_catastrophe_text')->value,
          'child_mortality' => $country->get('field_child_mortality')->value,
          'enrollment_rate' => $country->get('field_enrollment_rate')->value,
          'doctors_per_inhabitant' => $country->get('field_doctors_per_inhabitant')->value,
          'image' => $image,
          'life_expectancy' => $country->get('field_life_expectancy')->value,
          'sponsorship_children' => $country->get('field_sponsorship_children')->value,
          'success_headline' => $country->get('field_success_headline')->value,
          'success_text' => $country->get('field_success_text')->value,
          'success_link' => $success_link,
          'without_sponsorship' => $country->get('field_without_sponsorship')->value,
        ];
      }
    }
    return $countrys;
  }
}
