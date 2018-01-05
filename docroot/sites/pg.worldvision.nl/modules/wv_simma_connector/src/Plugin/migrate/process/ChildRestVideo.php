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
 * Fetch the video url from external REST server.
 *
 * @MigrateProcessPlugin(
 *   id = "child_rest_video"
 * )
 */
class ChildRestVideo extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   *
   * Split the 'administer nodes' permission from 'access content overview'.
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
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
        'media_code' => 'VID',
        'content_type_code' => 'CGV',
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
          case $video_url = $rows->xpath('csi_derivative_response[name="Web"]')[0];
            $video_url = (string) $video_url->url;
            break;

          case $video_url = (string) $xml->csi_response_query
            ->csi_response_row->csi_response_header_data->csi_media_path;
            break;

          case $video_url = $rows->xpath('csi_derivative_response[name="Mob"]')[0];
          case $video_url = $rows->xpath('csi_derivative_response[name="iPad"]')[0];
          case $video_url = $rows->xpath('csi_derivative_response[name="vThumb"]')[0];
            $video_url = (string) $video_url->url;
            break;
        }
      }
    } catch (RequestException $e) {
      watchdog_exception('wv_simma_connector', $e);
    }

    // If we have no image skip item.
    if (empty($video_url)) {
      return NULL;
    }

    return $video_url;
  }

}
