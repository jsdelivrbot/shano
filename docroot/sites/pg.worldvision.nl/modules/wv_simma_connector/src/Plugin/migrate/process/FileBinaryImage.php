<?php

/**
 * @file
 * Image binary file import migrate process plugin.
 */

namespace Drupal\wv_simma_connector\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Fetch the file & replace the value with file id.
 *
 * @MigrateProcessPlugin(
 *   id = "file_binary_image"
 * )
 */
class FileBinaryImage extends ProcessPluginBase {

  /**
   * Get image mime.
   *
   * @param $binary
   * @return string
   */
  public function get_image_file_type_from_binary($binary) {
    if (!preg_match(
      '/\A(?:(\xff\xd8\xff)|(GIF8[79]a)|(\x89PNG\x0d\x0a)|(BM)|(\x49\x49(?:\x2a\x00|\x00\x4a))|(FORM.{4}ILBM))/',
      $binary, $hits)) {
      return 'application/octet-stream';
    }

    static $type = array (
      1 => 'image/jpeg',
      2 => 'image/gif',
      3 => 'image/png',
      4 => 'image/x-windows-bmp',
      5 => 'image/tiff',
      6 => 'image/x-ilbm',
    );

    return $type[count($hits) - 1];
  }

  /**
   * {@inheritdoc}
   *
   * Split the 'administer nodes' permission from 'access content overview'.
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    // If we have a binary image string store it to the file.
    if (!$img = @imagecreatefromstring($value)) {
      // Skip this item if there's no image.
      return NULL;
    }

    // Get image type & file extension to prepare file name as we have only binary data.
    $mime = $this->get_image_file_type_from_binary($value);
    $ext = explode('/', $mime )[1];

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

      // Save the file, return its ID.
      $file = file_save_data($value, $public_path, FILE_EXISTS_REPLACE);
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
