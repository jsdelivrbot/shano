<?php

/**
 * @file
 * Contains the 'SEPA' payment_gateway.
 */

namespace Drupal\fs_payment_payone\Plugin;

use Drupal\Core\Url;
use Drupal\forms_suite\Entity\Form;
use Drupal\forms_suite\Entity\Message;
use Drupal\payment_gateway\Plugin\PaymentGatewayBase;
use Drupal\payone\PayOneService;

/**
 * Provides the base functions for payone
 * )
 */
abstract class PayoneBase extends PaymentGatewayBase {

  /**
   * Generates the external payment link for payone.
   * @param $args
   * @return mixed
   *  Redirect link
   */
  public function externalPaymentLink($args){

    /** @var Form $form_entity */
    $form_entity = $args['form_entity'];
    /** @var Message $message */
    $message = $args['message'];
    $values = $args['values'];

    $pay_one_args = $this->createArgList($values);

    $url_success = Url::fromRoute(
      'forms_suite.form.external',
      ['form' => $form_entity->getOriginalId(),'uuid' => $message->get('uuid')->getString()],
      ['absolute' => TRUE, $_SERVER['REQUEST_SCHEME'] => TRUE]);
    $url_back = Url::fromRoute(
      'entity.form.canonical',
      ['form' => $form_entity->getOriginalId()],
      ['absolute' => TRUE, $_SERVER['REQUEST_SCHEME'] => TRUE]);


    $web_reference_id = $message->id();

    // load ivision config
    $ivision_config = \Drupal::configFactory()->get('ivision.config');
    $ivision_config = $ivision_config->get('ivision');

    // Add the user given webReferenceID to the start value of the iVision webReferenceID,
    // if it is set in the config.
    if ($ivision_config !== NULL && isset($ivision_config['web_reference_id'])) {
      $web_reference_id += $ivision_config['web_reference_id'];
    }

    $pay_one_args += [
      'reference' => $web_reference_id,
      'narrative_text' => 'Ihre Spende an World Vision',
      'item_name' => 'Ihre Spende an World Vision',
      'successurl' => $url_success->toString(),
      'backurl' => $url_back->toString(),
    ];

    switch ($values['field_payment_method']['paymentMethod']){
      case 2:
        $payment_method = 'paypal';
        break;
      case 3:
        $payment_method = 'credit_card';
        break;
      default:
        $payment_method = 'paypal';
    }

    /** @var PayOneService $payone */
    $payone = \Drupal::service('payone');

    $link = $payone->buildRedirectLink($pay_one_args, $payment_method, $form_entity->get('id'), $form_entity->get('label'));

    return $link['redirecturl'];
  }

  /**
   * Modifies the value list for payone.
   *
   * @param $values
   * @return array
   */
  private function createArgList($values)
  {
    $pay_one_args = [];

    foreach ($values as $field) {
      foreach ($field as $element_id => $element_value) {
        switch ((string)$element_id) {
          case 'amount' :
            $pay_one_args['amount'] = $element_value * 100;
            break;
          case 'firstName' :
            $pay_one_args['firstname'] = $element_value;
            break;
          case 'surname' :
            $pay_one_args['lastname'] = $element_value;
            break;
          case 'companyName' :
            $pay_one_args['company'] = $element_value;
            break;
          case 'street' :
            $pay_one_args['street'] = $element_value;
            break;
          case 'postCode' :
            $pay_one_args['zip'] = $element_value;
            break;
          case 'city' :
            $pay_one_args['city'] = $element_value;
            break;
          case 'email' :
            $pay_one_args['email'] = $element_value;
            break;
          case 'countryCode' :
            $pay_one_args['country'] = $element_value;
            break;
        }
      }
    }
    return $pay_one_args;
  }
}

