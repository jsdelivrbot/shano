<?php
/**
 * User: MoeVoe
 * Date: 04.06.16
 * Time: 22:00
 */

namespace Drupal\ivision_nav16;

use Drupal\datasourcehandler\DatasourceIncidentInterface;
use Drupal\forms_suite\Entity\Message;
use Drupal\ivision_nav16\iVisionController\IVisionException;
use Drupal\ivision_nav16\iVisionController\IVisionIncident as IVisionControllerIncident;
use Drupal\ivision_nav16\iVisionController\IVisionLogger;
use Drupal\node\Entity\Node;


/**
 * Class IVision
 * @package Drupal\ivision
 */
class IVisionNav16Incident implements DatasourceIncidentInterface {

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
      'webReferenceID' => $externalReferenceNumber,
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
      'ActionCode' => 0,
      'billingPeriod' => 0,
      'paymentMethod' => '',
      'bankName' => '',
      'bankBranchNo' => '',
      'bankAccountNo' => '',
      'swiftCode' => '',
      'IBAN' => '',
      'externalReferenceNumber' => 'PAYONE-' . $externalReferenceNumber,
      'amountPerPeriod' => 0,
      'productCode' => '',
      'incidentType' => $incident_type,
      'childSequenceNo' => '',
      'childCountryCode' => '',
      'childGender' => '',
      'continent' => '',
      'catalogueID' => '',
      'catalogueQuantity' => '',
      'Comment1' => 'Form: ' . $form_name,
      'pledgeType' => '',
      'NoOfBelongingIncidents' => 1,
      'extraData' => ''
    ];

    self::renderFields($post, $args);
    self::specialConditions($post);
    self::translateBillingPeriod($post['billingPeriod']);
    self::splitLongComments($post);
    self::cutFieldLength($post['city']);
    self::cutFieldLength($post['phonePrivate']);
    self::cutFieldLength($post['postCode'], 6);
    self::cutFieldLength($post['bankBranchNo'], 8);

    $post = self::upperCaseKeys($post);

    $logger = new IVisionLogger();
    try {
      IVisionControllerIncident::incident($post, $logger);
    } catch (IVisionException $e) {
      \Drupal::logger('ivision_nav16')->error($logger->renderLogs());
      return $e->getMessage();
    }
    \Drupal::logger('ivision_nav16')->notice($logger->renderLogs());
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
            $post['donationReceipt'] = $value[key($value)];
            unset($value[key($value)]);
            foreach ($value as $year) {
              $post['donationReceipt'] .= ', ' . $year;
            }
            $additional_elements['donationReceipt'] = $post['donationReceipt'];
            break;
          case 'birthday':
          case 'birthmonth':
          case 'birthyear':
            $birthdate[$key] = $value;
            break;
          case 'survey':
            if (is_numeric($value)) {
              if ($post['ActionCode'] > 0) {
                $additional_elements['initial_motivationCode'] = $post['ActionCode'];
              }
              $post['ActionCode'] = $value;
            }
            else {
              // todo something with survey without motivation code
            }
            break;
          case 'motivationCode':
            if ($post['ActionCode'] > 0) {
              $additional_elements['initial_motivationCode'] = $value;
            }
            else {
              $post['ActionCode'] = $value;
            }
            break;
          case 'designationID':
            $post['PurposeCode'] = (int) $value;
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
              $additional_elements['gift price'] = (int) $node->get('field_gift_price')->value * $gifts['quantity'];
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


            // load ivision config and calculate original transfer_id
            $ivision_config = \Drupal::configFactory()->get('ivision.config');
            $ivision_config = $ivision_config->get('ivision');
            $transfer_id = $post['webReferenceID'];
            if ($ivision_config !== NULL && isset($ivision_config['web_reference_id'])) {
              $transfer_id = $post['webReferenceID'] - $ivision_config['web_reference_id'];
            }

            // load  message object
            /** @var Message $message */
            $message = \Drupal::entityTypeManager()
              ->getStorage('forms_message')
              ->load($transfer_id);

            // count message children + parent.
            if ($message_children = $message->getEntityChildren()) {
              $post['NoOfBelongingIncidents'] = count($message_children) + 1;
            }
            elseif ($message_referenced = $message->getReferencedEntity()) {
              $post['NoOfBelongingIncidents'] = count($message_referenced->getEntityChildren()) + 1;
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
      $post['Comment1'] .= ' * ' . $additional_element_key . ': ' . $additional_element_value;
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


  /**
   * Translate BillingPeriod numbers to new descriptions.
   * @param $billingPeriod
   *  Int number of the billingPeriod.
   * @return string
   */
  private static function translateBillingPeriod(&$billingPeriod) {
    switch ($billingPeriod) {
      case 1:
        return $billingPeriod = 'Week';
        break;
      case 2:
        return $billingPeriod = 'Month';
        break;
      case 3:
        return $billingPeriod = '2 Months';
        break;
      case 4:
        return $billingPeriod = 'Quarter';
        break;
      case 5:
        return $billingPeriod = 'Half Year';
        break;
      case 6:
        return $billingPeriod = 'Year';
        break;
      case 7:
        return $billingPeriod = 'Once';
        break;
      default :
        return $billingPeriod = '';
    }
  }


  /**
   * Upper case the first char of all array keys.
   * @param $args
   *  Source args.
   * @return mixed
   */
  public static function upperCaseKeys($args) {
    $return = [];
    foreach ($args as $arg_key => $arg) {
      $return[ucfirst($arg_key)] = $arg;
    };
    return $return;
  }

  /**
   * NAV16 only allow 250 chars in the comment fields.
   * The function splits long strings in the four comment fields.
   *
   * @param $args
   *  List of incident arguments.
   */
  public static function splitLongComments(&$args) {
    if (mb_strlen($args['Comment1']) > 250) {
      $args['Comment2'] = mb_substr($args['Comment1'], 251);
      $args['Comment1'] = mb_substr($args['Comment1'], 0, 250);
      if (mb_strlen($args['Comment2']) > 250) {
        $args['Comment3'] = mb_substr($args['Comment2'], 251);
        $args['Comment2'] = mb_substr($args['Comment2'], 0, 250);
        if (mb_strlen($args['Comment3']) > 250) {
          $args['Comment4'] = mb_substr($args['Comment3'], 251);
          $args['Comment3'] = mb_substr($args['Comment3'], 0, 250);
          if (mb_strlen($args['Comment4']) > 250) {
            $args['Comment4'] = mb_substr($args['Comment4'], 0, 250);
          }
        }
      }
    }
  }

  /**
   * Cut the Length of field's to prevent NAV16 errors.
   *
   * @param $field
   * @param int $length
   */
  public static function cutFieldLength(&$field, $length = 30) {
    if(strlen($field) > $length){
      $field = substr($field, 0, $length);
    }
  }

}
