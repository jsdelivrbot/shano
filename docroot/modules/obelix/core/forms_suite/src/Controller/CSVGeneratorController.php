<?php

namespace Drupal\forms_suite\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Field\FieldItemList;
use Drupal\csv_serialization\Encoder\CsvEncoder;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\forms_suite\DataHandler;
use Drupal\forms_suite\Entity\Message;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class CSVGeneratorController.
 *
 * @package Drupal\forms_suite\Controller
 */
class CSVGeneratorController extends ControllerBase
{

  /**
   * Symfony\Component\HttpFoundation\Session\Session definition.
   *
   * @var Session
   */
  protected $session;

  /**
   * {@inheritdoc}
   */
  public function __construct(Session $session)
  {
    $this->session = $session;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container)
  {
    return new static(
      $container->get('session')
    );
  }

  /**
   * Generate csv.
   *
   * Create a csv with the csv session data and the depending field values.
   * The Session is set in the forms_preprocess_views_view_table(&$variables);
   *
   * @return Response
   *   Return a CSV with filtered output from the submitted forms view.
   */
  public function generateCSV()
  {

    // the session is set in a hook of the submitted forms table
    // hold all rows of the last filtered request.
    $tempstore = \Drupal::service('user.private_tempstore')->get('forms');
    $rows = $tempstore->get('csv');
    if (isset($rows['rows'])) {
      $rows = $rows['rows'];

      $entity_manager = $this->entityTypeManager()->getStorage('forms_message');

      // data array for the csv
      $data = [];
      // saves all header of the csv columns.
      // necessary to handle empty fields.
      $new_headers = [];

      foreach ($rows as $row) {
        $row_set = [];

        // crawl the output from the submitted table and save the values in the $row_set
        foreach ($row['columns'] as $item_key => $item) {
          /** @var \Drupal\Core\Render\Markup $value */
          $value = $item['content'][0]['field_output']['#markup'];
          $value = stripslashes(preg_replace('/<!--(.|\s)*?-->/', '', $value));
          $value = filter_var($value, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
          $item_key = str_replace('_', ' ', $item_key);
          $row_set[$item_key] = $value;
          $new_headers[$item_key] = '';
        }

        // delete unwanted columns
        unset($row_set['nothing']);
        unset($new_headers['nothing']);

        // load the Message Entity of the actually row.
        // need to get the attached fields and the attached field values.
        $messages = $entity_manager->loadByProperties(['uuid' => $row_set['uuid']]);
        foreach ($messages as $message) {
          /** @var Message $message */
          $all_fields = $message->getFields();

          // all custom plugin fields which are attached to the Message entity.
          $fields = DataHandler::searchFields($all_fields);

          // crawl all fields
          foreach ($fields as $field_key => $field) {
            /** @var FieldItemList $field */
            /** @var FieldItemList $field_values */
            $field_values = $message->$field_key;

            // if field has values or is attached to the entity storage
            // have to detect all property definitions (Multi elements in one field)
            if ($field_values[0] !== NULL) {
              /** @var FieldConfig $field_definitions */
              $field_definitions = $field_values->getFieldDefinition();
              /** @var FieldStorageConfig $field_storage */
              $field_storage = $field_definitions->get('fieldStorage');
              $field_property_definitions = $field_storage->getPropertyDefinitions();

              // crawl the property's and save the values in $row_set and $new_new_headers.
              foreach ($field_property_definitions as $property_name => $property_definition) {
                $field_key = str_replace('_', ' ', $field_key);
                $value = $field_values[0]->$property_name;
                $property_name = str_replace('_', ' ', $property_name);
                $charset =  mb_detect_encoding(
                      $value,
                      "UTF-8, ISO-8859-1, ISO-8859-15",
                      true
                  );
                $value =  mb_convert_encoding($value, "Windows-1252", $charset);

                If($field_key == "field donation period" OR $field_key == "field donation period interval" OR $field_key == "field donation period single"){
                    if($value == "2") {
                        $value = t('one time');
                    }
                    if($value == "7") {
                        $value = t('every month');
                    }
                }
                If($field_key == "field payment method") {
                    if($value == "1") {
                        $value = t('Bank');
                    }
                    if($value == "2") {
                        $value = t('Paypal');
                    }
                    if($value == "3") {
                        $value = t('Credit card');
                    }
                }
                $new_headers[$field_key . ' - ' . $property_name] = '';
                //$row_set[$field_key . ' - ' . $property_name] = $field_values[0]->$property_name;
                $row_set[$field_key . ' - ' . $property_name] = $value;
              }
            }
          }
        }
        $data[] = $row_set;
      }


      // make sure that every row in the data set have the same columns.
      foreach ($data as &$row) {
        $row = array_merge($new_headers, $row);
      }

      // create csv data
      $csv_encoder = new CsvEncoder(';');

      $csv = $csv_encoder->encode($data, 'csv' );

      $response = new Response($csv, 200, [
        'Content-Type:' => 'text/csv; charset=utf-8',
        'Content-Disposition' => 'attachment; filename=submitted_forms.csv',
      ]);

      return $response;
    }
  }


}
