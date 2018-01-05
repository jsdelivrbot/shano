<?php

/**
 * @file
 * Migrate process plugin.
 */

namespace Drupal\wv_simma_connector\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use GuzzleHttp\Exception\RequestException;

/**
 * Fetch the image from external REST server.
 *
 * @MigrateProcessPlugin(
 *   id = "child_rest_image"
 * )
 */
class ChildRestImage extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   *
   * Split the 'administer nodes' permission from 'access content overview'.
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    // Hardcode extension to avoid the need to do http request to external API for every already imported child only
    // for extension check. We assume that all images are jpg. If needed to process other images types it will hurt
    // import processing speed.
    $ext = 'jpg';

    // Use plugin config to generate filename.
    if (empty($this->configuration['path_sources'])) {
      $this->configuration['path_sources'][] = 'id';
    }

    $values = [];
    $tokens = implode('-', array_fill(0, count($this->configuration['path_sources']), '%s'));

    // Get all values that are requested in config & map it
    // to printf format like '%s %s %s', ['string1', 'str2', ...].
    array_map(function ($property) use ($row, &$values) {
      $values[] = $row->getSourceProperty($property);
    }, $this->configuration['path_sources']);

    $path = vsprintf("child-$tokens", $values);

    // Delete spaces, lowercase & do other clean ops.
    $path = \Drupal::service('pathauto.alias_cleaner')->cleanString($path);
    $path = "$path.$ext";

    $public_path = 'public://children/' . $path;

    if (!file_exists($public_path) || !empty($this->configuration['force'])
      || (!$files = \Drupal::entityTypeManager()->getStorage('file')->loadByProperties(['uri' => $public_path]))) {

      $public_dirname = dirname($public_path);

      // create directories if necessary
      if (!file_exists($public_dirname)) {
        file_prepare_directory($public_path, 0775);
      }

      // Fetch meta data from API & then download image.
      $client = \Drupal::httpClient();
      $config = \Drupal::config('wv_simma_connector.settings.migrate');
      $method = 'GET';

      $options = [
        'auth' => [
          $config->get('media_endpoint_user'),
          $config->get('media_endpoint_pass'),
        ],
        'headers' => [
          'wv-version' => 'v500',
        ],
        'query' => [
          'child_code' => $row->getSourceProperty('child_project') . '-' . $row->getSourceProperty('childsequence'),
          'media_code' => 'PIC',
          'content_type_code' => 'CGP',
          'approval_status_code' => 0,
          'result_set_size' => 1,
        ],
      ];

      try {
        $response = $client->request($method, $config->get('media_endpoint'), $options);
        $code = $response->getStatusCode();

        if ($code == 200) {
          $body = $response->getBody()->getContents();

          $xml = new \SimpleXMLElement($body);
          $rows = $xml->csi_response_query->csi_response_row;
          switch (TRUE) {
            case $img_url = (string) $xml->csi_response_query->csi_response_row->csi_response_header_data->csi_media_path;
              break;
            case $img_url = $rows->xpath('csi_derivative_response[name="original"]')[0];
            case $img_url = $rows->xpath('csi_derivative_response[name="web"]')[0];
            case $img_url = $rows->xpath('csi_derivative_response[name="thumb"]')[0];
              $img_url = (string) $img_url->url;
              break;
          }
        }
      } catch (RequestException $e) {
        watchdog_exception('wv_simma_connector', $e);
      }

      // If we have no image skip item.
      if (empty($img_url) || (!$binary = file_get_contents($img_url))) {
        return NULL;
      }

      // Save the file, return its ID.
      $file = file_save_data($binary, $public_path, FILE_EXISTS_REPLACE);
    } else {
      $file = reset($files);
    }

    if (empty($file)) {
      throw new \Drupal\migrate\MigrateSkipProcessException();
    }

    $additional_field_data = [];

    // Get dynamic source from parsed data.
    if (!empty($this->configuration['alt_source'])
      && ($alt = $row->getSourceProperty($this->configuration['alt_source']))) {

      $additional_field_data['alt'] = $alt;
    }

    return [
      'target_id' => $file->id(),
    ] + $additional_field_data;
  }

}
