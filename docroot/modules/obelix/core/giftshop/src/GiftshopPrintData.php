<?php

/**
 * @files
 * Contains the print data export service.
 *
 * @see \Drupal\giftshop\GiftshopPrintDataExport
 */

namespace Drupal\giftshop;

use Drupal\Core\Field\FieldItemList;
use Drupal\csv_serialization\Encoder\CsvEncoder;
use Drupal\forms_suite\DataHandler;
use Drupal\forms_suite\MessageInterface;
use Drupal\node\Entity\Node;
use XLSXWriter;

/**
 * Class GiftshopPrintDataExport
 *
 * @package Drupal\giftshop
 */
class GiftshopPrintData implements GiftshopPrintDataInterface {

  /**
   * {@inheritdoc}
   */
  public function getData($from, $to) {

    $cvs_structure = [
      'Bestell-ID' => '',
      'Anrede' => '',
      'Titel' => '',
      'Vorname' => '',
      'Nachname' => '',
      'Firma' => '',
      'Strasse' => '',
      'Nr' => '',
      'PLZ' => '',
      'Ort' => '',
      'Land' => '',
      180 => '',
      190 => '',
      200 => '',
      210 => '',
      220 => '',
      230 => '',
      240 => '',
      250 => '',
      260 => '',
      270 => '',
      280 => '',
      290 => '',
      300 => '',
      310 => '',
      320 => '',
      330 => '',
      340 => '',
      350 => '',
      360 => '',
      370 => '',
      390 => '',
      400 => '',
      410 => '',
      420 => '',
      430 => '',
      440 => '',
      181 => '',
      191 => '',
      201 => '',
      211 => '',
      221 => '',
      231 => '',
      241 => '',
      251 => '',
      261 => '',
      271 => '',
      281 => '',
      291 => '',
      301 => '',
      311 => '',
      321 => '',
      331 => '',
      341 => '',
      351 => '',
      361 => '',
      371 => '',
      391 => '',
      401 => '',
      411 => '',
      421 => '',
      431 => '',
      441 => '',
      'Motiv' => '',
      'Empfaenger Freitext' => '',
      'Gruss Freitext' => '',
      'Absender Freitext' => '',
      'Anrede card' => '',
      'Gruss card' => '',
    ];


    $data[] = $cvs_structure;
    $message_entity_type = \Drupal::entityTypeManager()
      ->getStorage('forms_message');
    $message_giftshop = $message_entity_type->getQuery()
      ->condition('field_giftshop.gifts', '', '!=')
      ->condition('changed', $from, '>')
      ->condition('changed', $to, '<')
      ->condition('entity_reference', 0, '=')
      ->condition('transfer_id', 0, '>=')
      ->execute();

    $message_giftshop = $message_entity_type->loadMultiple($message_giftshop);

    $count = 0;
    foreach ($message_giftshop as $message) {
      /** @var MessageInterface $message */

      $values = $this->getValue($message);
      $gift = unserialize($values['field_giftshop']['gifts']);

      if (!empty($message_bundle = $message->getEntityChildren())) {
        foreach ($message_bundle as $message_bundle_item) {
          /** @var MessageInterface $message_bundle_item */
          $values_bundle_item = $this->getValue($message_bundle_item);
          $gift_bundle_item = unserialize($values_bundle_item['field_giftshop']['gifts']);
          if ($gift_bundle_item['response_type'] == 'card') {
            $message_giftshop[] = $message_bundle_item;
          }

        }
      }
    }


    foreach ($message_giftshop as $message) {
      /** @var MessageInterface $message */

      $values = $this->getValue($message);
      $gift = unserialize($values['field_giftshop']['gifts']);

      $message_bundle_certificates = [];
      // Initial quantity of main product.
      if ($gift['response_type'] == 'certificate' && $gift['response_data']['send_type'] !== 'email') {
        $message_bundle_certificates[$gift['gift_id'] * 10] = $gift['quantity'];
      }

      if (!empty($message_bundle = $message->getEntityChildren())) {
        foreach ($message_bundle as $message_bundle_item) {
          /** @var MessageInterface $message_bundle_item */
          $values_bundle_item = $this->getValue($message_bundle_item);
          $gift_bundle_item = unserialize($values_bundle_item['field_giftshop']['gifts']);
          if ($gift_bundle_item['response_type'] == 'certificate' && $gift_bundle_item['response_data']['send_type'] !== 'email') {

            if (empty($message_bundle_certificates[$gift_bundle_item['gift_id'] * 10])) {
              $message_bundle_certificates[$gift_bundle_item['gift_id'] * 10] = 0;
            }
            $message_bundle_certificates[$gift_bundle_item['gift_id'] * 10] += $gift_bundle_item['quantity'];

          }
        }
      }


      if (!($gift['response_type'] == 'certificate' && $gift['response_data']['send_type'] == 'email') && is_array($gift)) {
        $count++;
        foreach ($cvs_structure as $element => $element_value) {
          $key = $element;
          if (is_numeric($element)) {
            if ($gift['response_type'] == 'certificate') {
              $gift_id_format_100 = $gift['gift_id'] * 10;
            }
            elseif ($gift['response_type'] == 'card') {
              $gift_id_format_100 = $gift['gift_id'] * 10 + 1;
            }
            if ($gift_id_format_100 == $element || isset($message_bundle_certificates[$element])) {
              $element = $gift['quantity'];
              // Set the proper quantities if sub-element exists.
              if (!empty($message_bundle_certificates[$key])) {
                $element = $message_bundle_certificates[$key];
              }
            }
            else {
              $element = 0;
            }
          }
          else {
            if (
              !empty($gift['response_data']['additional_delivery']['firstName']) &&
              !empty($gift['response_data']['additional_delivery']['surname']) &&
              !empty($gift['response_data']['additional_delivery']['street']) &&
              !empty($gift['response_data']['additional_delivery']['houseNo']) &&
              !empty($gift['response_data']['additional_delivery']['postCode']) &&
              !empty($gift['response_data']['additional_delivery']['city'])
            ) {
              $user_data = $gift['response_data']['additional_delivery'];
            }
            else {
              $user_data = $values['field_user_data'];
            }

            switch ($element) {
              case 'Bestell-ID' :
                $element = $message->id();
                break;
              case 'Anrede' :

                switch ($user_data['salutation']) {
                  case 1:
                    $element = "Herr";
                    break;
                  case 2:
                    $element = "Frau";
                    break;
                  default:
                    $element = "";
                    break;
                }
                break;
              case 'Titel' :
                switch ($user_data['jobTitleCode']) {
                  case 1:
                    $element = "Dr.";
                    break;
                  case 14:
                    $element = "Prof.";
                    break;
                  default:
                    $element = "";
                    break;
                }
                break;
              case 'Vorname' :
                $element = $user_data['firstName'];
                break;
              case 'Nachname' :
                $element = $user_data['surname'];
                break;
              case 'Firma' :
                $element = $user_data['companyName'];
                break;
              case 'Strasse' :
                $element = $user_data['street'];
                break;
              case 'Nr' :
                $element = (string) $user_data['houseNo'];
                break;
              case 'PLZ' :
                $element = $user_data['postCode'];
                break;
              case 'Ort' :
                $element = $user_data['city'];
                break;
              case 'Land' :
                $element = $user_data['country'];
                break;
              case 'Motiv' :
                if (isset($gift['response_data']['card_type'])) {
                  $element = ++$gift['response_data']['card_type'];
                }
                else {
                  $element = '';
                }
                break;
              case 'Empfaenger Freitext' :
                $element = $gift['response_data']['card_to'];
                break;
              case 'Gruss Freitext' :
                $element = $gift['response_data']['card_message'];
                break;
              case 'Absender Freitext' :
                $element = $gift['response_data']['card_from'];
                break;
              case 'Anrede card' :
                $element = $gift['response_data']['card_to_salutation'];
                break;
              case 'Gruss card' :
                $element = $gift['response_data']['card_from_greetings'];
                break;
            }
          }
          $data[$count][$key] = $element;
        }
      }

    }
    return $data;
  }

  /**
   * {@inheritdoc}
   */
  public function exportXLSX($from, $to = REQUEST_TIME) {
    $data = $this->getData($from, $to);

    // Generate xlsx.
    foreach ($data[0] as $key => $value) {
      $data[0][$key] = $key;
    }
    $writer = new XLSXWriter();
    $writer->writeSheet($data);
    $xlsx = $writer->writeToString();

    return $xlsx;
  }

  /**
   * @param MessageInterface $message
   * @return mixed
   */
  private function getValue(MessageInterface $message) {
    $data_handler = new DataHandler($message->getFields(), $message, ['empty' => []]);
    foreach ($data_handler->searchFields($message->getFields()) as $field_name => $field) {
      /** @var FieldItemList $field */
      $values[$field_name] = $field->getValue()[0];
    }
    return $values;
  }

}
