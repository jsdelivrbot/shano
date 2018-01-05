<?php
/**
 * User: MoeVoe
 * Date: 04.06.16
 * Time: 22:00
 */

namespace Drupal\ivision;

use Drupal\datasourcehandler\DatasourceIncidentInterface;
use Drupal\ivision\iVisionController\IVisionException;
use Drupal\ivision\iVisionController\IVisionIncident as IVisionControllerIncident;
use Drupal\node\Entity\Node;


/**
 * Class IVision
 * @package Drupal\ivision
 */
class IVisionIncident implements DatasourceIncidentInterface {

  /**
   * @inheritdoc
   */
  public static function setIncident(array $args, $incident_type, $web_reference_id, $form_name, $parent_web_reference_id = NULL) {
    // load ivision config
    $ivision_config = \Drupal::configFactory()->get('ivision.config');
    $ivision_config = $ivision_config->get('ivision');

    // If present use the parent web reference id otherwise use the default web reference id.
    $externalReferenceNumber = $parent_web_reference_id ? $parent_web_reference_id : $web_reference_id;

    // Add the user given webReferenceID to the start value of the iVision webReferenceID,
    // if it is set in the config.
    if ($ivision_config !== NULL && isset($ivision_config['web_reference_id'])) {
      $web_reference_id += $ivision_config['web_reference_id'];
      $externalReferenceNumber += $ivision_config['web_reference_id'];
    }

    // define default values
    $post = [
      'webReferenceID' => $web_reference_id,
      'partnerID' => 0,
      'salutation' => 0,
      'firstName' => '',
      'surname' => '',
      'jobTitleCode' => '',
      'companyName1' => '',
      'street' => '',
      'houseNo' => '',
      'postCode' => '',
      'city' => '',
      'countryCode' => '',
      'email' => '',
      'phonePrivate' => '',
      'mobilePhoneNo' => '',
      'faxNo' => '',
      'birthdate' => '',
      'importDateBuf' => date("Y-m-d") . " 00:00:00.000",
      'importTimeBuf' => "1754-01-01 " . date("H:i:s") . ".000",
      'motivationCode' => 0,
      'billingPeriod' => 0,
      'paymentMethod' => '',
      'bankName' => '',
      'bankBranchNo' => '',
      'bankAccountNo' => '',
      'swiftCode' => '',
      'IBAN' => '',
      'externalReferenceNumber' => 'PAYONE-' . $externalReferenceNumber,
      'amountPerPeriod' => 0,
      'month13' => 0,
      'productCode' => '',
      'incidentType' => $incident_type,
      'childSequenceNo' => '',
      'childCountryCode' => '',
      'childGender' => '',
      'continent' => '',
      'designationID' => 0,
      'catalogueID' => 0,
      'catalogueQuantity' => 0,
      'note' => 'Form: ' . $form_name,
      'pledgeType' => '',
      'extraData' => ''
    ];

    self::renderFields($post, $args);
    self::specialConditions($post);

    try {
      IVisionControllerIncident::setComfortSolutionIncident($post);
    } catch (IVisionException $e) {
      return $e->getMessage();
    }

    return $web_reference_id;
  }

  /**
   * Parse all Fields and overwrites values of post array.
   * Build datetime from single elements.
   * Create comment field.
   *
   * @param array $post
   *  values send to iVision.
   * @param array $args
   *  values from the form.
   */
  private static function renderFields(array &$post, array $args) {
    $additional_elements = [];
    $birthdate = [];

    // parse all fields and build post array for ivision
    foreach ($args as &$field) {
      foreach ($field as $key => $value) {
        switch ($key) {
          case 'interval':
            $post['billingPeriod'] = self::convertDonationIntervalToIVisionBillingPeriod($value, 2);
            break;
          case 'amount':
            $post['amountPerPeriod'] = number_format((float) $value, 1, '.', '');
            break;
          case 'childGender':
            switch (child_gender_check($value)) {
              case 'female':
                $post['childGender'] = 'F';
                break;

              case 'male':
                $post['childGender'] = 'M';
                break;
            }
            break;
          case 'companyName':
            $post['companyName1'] = $value;
            break;
          case 'countryCode':
            if ($value == 'DE') {
              $post['countryCode'] = '';
            }
            else {
              $post['countryCode'] = $value;
            }
            break;
          case 'donationReceipt':
            reset($value);
            $additional_elements['donation_receipt'] = $value[key($value)];
            unset($value[key($value)]);
            foreach ($value as $year) {
              $additional_elements['donation_receipt'] .= ', ' . $year;
            }
            break;
          case 'birthday':
          case 'birthmonth':
          case 'birthyear':
            $birthdate[$key] = $value;
            break;
          case 'survey':
            if (is_numeric($value)) {
              if ($post['motivationCode'] > 0) {
                $additional_elements['initial_motivationCode'] = $post['motivationCode'];
              }
              $post['motivationCode'] = $value;
            }
            else {
              // todo something with survey without motivation code
            }
            break;
          case 'motivationCode':
            if ($post['motivationCode'] > 0) {
              $additional_elements['initial_motivationCode'] = $value;
            }
            else {
              $post['motivationCode'] = $value;
            }
            break;
          case 'headline':
          case 'subline':
            // prevent from rendered in iVision json.
            break;
          case 'gifts':
            // giftshop data handling
            $gifts = unserialize($value);
            $post['pledgeType'] = '';
            $post['catalogueID'] = $gifts['gift_id'];
            $post['catalogueQuantity'] = $gifts['quantity'];
            $post['amountPerPeriod'] = 0;

            $additional_elements['Versand'] = 'DMS';

            if ($node = Node::load($gifts['node_id'])) {
              $additional_elements['gift name'] = $node->label();
//              $additional_elements['gift price'] = (int) $node->get('field_gift_price')->value * $gifts['quantity'];
            }
            $additional_elements['gift type'] = $gifts['response_type'];
            if (isset($gifts['response_data']['send_type'])) {
              $additional_elements['transmitted by'] = $gifts['response_data']['send_type'];
            }
            else {
              $additional_elements['transmitted by'] = 'post';
            }
            if (isset($gifts['response_data']['card_type'])) {
              $additional_elements['card id'] = (int) $gifts['response_data']['card_type'];
              $additional_elements['card id']++;
            }
            if (
              !empty($gifts['response_data']['additional_delivery']['firstName']) &&
              !empty($gifts['response_data']['additional_delivery']['surname']) &&
              !empty($gifts['response_data']['additional_delivery']['street']) &&
              !empty($gifts['response_data']['additional_delivery']['houseNo']) &&
              !empty($gifts['response_data']['additional_delivery']['postCode']) &&
              !empty($gifts['response_data']['additional_delivery']['city'])
            ) {
              foreach ($gifts['response_data']['additional_delivery'] as $element_key => $element) {
                if (!empty($element)) {
                  $additional_elements['delivery ' . $element_key] = $element;
                }
              }
            }

            break;
          default :
            if (array_key_exists($key, $post)) {
              $post[$key] = $value;
            }
            else {
              $additional_elements[$key] = $value;
            }
        }
      }
    }


    // build birthday field for wovi ivision
    if (!empty($birthdate['birthday']) && !empty($birthdate['birthmonth']) && !empty($birthdate['birthyear'])) {
      $post['birthdate'] = implode('.', $birthdate);
      unset($additional_elements['birthday']);
      unset($additional_elements['birthmonth']);
      unset($additional_elements['birthyear']);
    }

    // Unset unnecessary fields
    unset($additional_elements['child']);

    // build comment field with additional fields
    foreach ($additional_elements as $additional_element_key => $additional_element_value) {
      $post['note'] .= ' * ' . $additional_element_key . ': ' . $additional_element_value;
    }
  }

  /**
   * Special Conditions from wovi germany.
   *
   * @param array $post
   *  values send to iVision.
   */
  private static function specialConditions(array &$post) {
    if (isset($post['paymentMethod'])) {

      // Setting incident type to annual donation when billig period is 6.
      if ($post['billingPeriod'] == 6) {
        $post['incidentType'] = 'JAEHRL. ZUSATZSPENDE';
      }

      // Special condition for PROJEKTPATENSCHAFTEN if it is a single period switch incident.
      if (
        $post['incidentType'] == 'PROJEKTPATENSCHAFTEN' &&
        (isset($post['billingPeriod']) && $post['billingPeriod'] == 7)
      ) {
        $post['incidentType'] = 'EINMALSPENDEN';
      }

      // incident type extra conditions for paypal and credit card
      if (($post['incidentType'] == 'EINMALSPENDEN') && ($post['paymentMethod'] == 2 || $post['paymentMethod'] == 3)) {
        $post['incidentType'] = 'EINMALSPENDEN SONDERZAHLUNG PAYPAL ETC.';
      }


      // translate payment method
      switch ($post['paymentMethod']) {
        case 1:
          if (isset($post['billingPeriod']) && $post['billingPeriod'] == 7) {
            $post['paymentMethod'] = "LAST-EINM";
          }
          else {
            $post['paymentMethod'] = "LAST";
          }
          break;
        case 2:
          $post['paymentMethod'] = "SONST";
          break;
        case 3:
          $post['paymentMethod'] = "KREDIT";
          break;
      }
    }
  }

  /**
   * Converts the Plugin Id of a Donation Interval to the internal iVision integer value.
   *
   * @param $plugin_id
   *   The Plugin Id of the Donation Interval
   * @param null $default
   *   A default value if the $plugin_id doesn't exists.
   * @return mixed
   */
  public static function convertDonationIntervalToIVisionBillingPeriod($plugin_id, $default = NULL) {
    $map = [
      'donation_interval_annual' => 6,
//      'donation_interval_seminnual' => 'Not yet invented.',
//      'donation_interval_quarterly' => 'Not yet invented.',
      'donation_interval_monthly' => 2,
      'donation_interval_one_time' => 7,
    ];

    return isset($map[$plugin_id]) ? $map[$plugin_id] : $default;
  }
}
