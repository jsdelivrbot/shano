<?php

namespace Drupal\ffw_child_list_management\Controller;

use Drupal\child\Entity\Child;
use Drupal\Core\Config\Entity\Query\Query;
use Drupal\Core\Controller\ControllerBase;

/**
 * Class ChildDataController.
 */
class ChildDataController extends ControllerBase {

  public static function getOptions($option) {
    $query = \Drupal::service('entity.query')
      ->get('child');
    $get_node_labels = \Drupal::service('entity_field.manager')->getFieldDefinitions('child', 'child');
    $field_label = $get_node_labels[$option]->get('label');
    $entity_ids = $query->execute();
    $all_childs = Child::loadMultiple($entity_ids);
    $data_to_csv = [];
    $data_to_csv[] = [
      ucfirst($field_label),
    ];
    $data_to_csv[] = [];
    $i = 0;
    $buffer = 'up';
    foreach ($all_childs as $child) {
      $data_fields = $child->get($option)->first()->getValue()['value'];
      $new_array = array_fill(0, $i, '');
      $new_array[] = $data_fields;
      $data_to_csv[] = $new_array;

      if ($i < 5 && $buffer == 'up') {
        $i++;
      }
      if ($i == 5) {
        $buffer = 'down';
        $i--;
      }
      if ($i > -1 && $buffer == 'down') {
        $i--;
      }
      if ($i == -1 && $buffer == 'down') {
        $buffer = 'up';
        $i+=2;
      }

    }
    return $data_to_csv;
  }




  public static function generateCsv($data, $delimiter = ',', $enclosure = '"') {
    $fp = fopen('php://output', 'w');
    header("Content-Type:text/csv");
    header("Content-Disposition: attachment; filename=books.csv");
    $contents = '';
    foreach ($data as $result)
    {
      fputcsv($fp, $result, $delimiter, $enclosure);
    }

    rewind($fp);
    while (!feof($fp)) {
      $contents .= fread($fp, 8192);
    }
    fclose($fp);

    echo $contents;

    exit();
  }


}
