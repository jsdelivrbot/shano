<?php

namespace Drupal\fs_payment\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\forms_suite\Plugin\Field\FieldWidget\FormsSuiteWidget;
use Drupal\payment_gateway\Plugin\PaymentGatewayManager;

/**
 * Plugin implementation of the 'wovi_payment_method_widget' widget.
 *
 * @FieldWidget(
 *   id = "wovi_payment_method_widget",
 *   multiple_values = TRUE,
 *   label = @Translation("wovi payment method widget"),
 *   field_types = {
 *     "wovi_payment_method_field"
 *   }
 * )
 */
class WoViPaymentMethodWidget extends FormsSuiteWidget
{

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state)
  {
    $element = parent::formElement($items, $delta, $element, $form, $form_state);


    $forms_suite_configs = $this->getFieldSetting('forms_suite_configs');
    if (isset($forms_suite_configs['external_payment']) && !$forms_suite_configs['external_payment']) {
      $payment_options = [
        1 => t('Bank'),
      ];
      $attributes_payment = [
        'class' => [
          'hidden',
        ],
      ];
    } else {
      /** @var PaymentGatewayManager $plugin_manager */
      $plugin_manager = \Drupal::service('plugin.manager.payment_gateway');
      $payment_options = $plugin_manager->getPaymentGatewayOptions();
      $attributes_payment = [];
    }

    $element['paymentMethod'] = [
      '#type' => 'radios',
      '#title' => t('Payment method'),
      '#default_value' => isset($items[$delta]->value) ? $items[$delta]->value : 1,
      '#options' => $payment_options,
      // @todo on change of visibility have to select radio 1
      '#states' => [
        'invisible' => [':input[name="field_donation_period[billingPeriod]"]' => ['value' => "2"]],
      ],
      '#ajax' => [
        'callback' => [get_class($this), 'validateFormAjax'],
        'event' => 'blur',
        'disable-refocus' => TRUE,
        'progress' => [
          'type' => 'throbber',
          'message' => t('validating'),
        ],
      ],
      '#attributes' => $attributes_payment,
      '#element_validate' => [[get_class($this), 'validateElement']],
      '#required' => TRUE,
    ];

    $element['IBAN'] = array(
      '#type' => 'textfield',
      '#title' => t('IBAN'),
      '#ajax' => [
        'callback' => [get_class($this), 'validateFormAjax'],
        'event' => 'blur',
        'disable-refocus' => TRUE,
        'progress' => [
          'type' => 'throbber',
          'message' => t('validating'),
        ],
      ],
      '#element_validate' => [[get_class($this), 'validateElement']],
    );

    $element['swiftCode'] = array(
      '#type' => 'textfield',
      '#title' => t('BIC (only on foreign accounts)'),
      '#ajax' => [
        'callback' => [get_class($this), 'validateFormAjax'],
        'event' => 'blur',
        'disable-refocus' => TRUE,
        'progress' => [
          'type' => 'throbber',
          'message' => t('validating'),
        ],
      ],
      '#element_validate' => [[get_class($this), 'validateElement']],
    );

    $element['bankAccountNo'] = array(
      '#type' => 'textfield',
      '#title' => t('Bank account number'),
      '#ajax' => [
        'callback' => [get_class($this), 'validateFormAjax'],
        'event' => 'blur',
        'disable-refocus' => TRUE,
        'progress' => [
          'type' => 'throbber',
          'message' => t('validating'),
        ],
      ],
      '#element_validate' => [[get_class($this), 'validateElement']],
    );

    $element['bankBranchNo'] = array(
      '#type' => 'textfield',
      '#title' => t('Bank branch number'),
      '#ajax' => [
        'callback' => [get_class($this), 'validateFormAjax'],
        'event' => 'blur',
        'disable-refocus' => TRUE,
        'progress' => [
          'type' => 'throbber',
          'message' => t('validating'),
        ],
      ],
      '#element_validate' => [[get_class($this), 'validateElement']],
    );

    return $element;
  }


  /**
   * {@inheritdoc}
   */
  public static function validateElement(array $element, FormStateInterface &$form_state)
  {
    $form_state->setLimitValidationErrors(NULL);
    $triggering_element = $form_state->getTriggeringElement();
    $section_values = $form_state->getValues()['field_payment_method'];

    // Check bank fields on submit (not on blur)
    if ($section_values['paymentMethod'] == 1 && $triggering_element['#type'] == 'submit') {
      if (
        empty($section_values['IBAN']) &&
        empty($section_values['swiftCode']) &&
        empty($section_values['bankAccountNo']) &&
        empty($section_values['bankBranchNo'])
      ) {
        $form_state->setError($element, t('Your bank data is required.'));
      }
    }

    switch (end($element['#parents'])) {
      case "IBAN" :
        if ($section_values['paymentMethod'] == 1) {

          // check if the field has to be set
          if (
            empty($section_values['IBAN']) &&
            !empty($section_values['swiftCode']) &&
            (
              empty($section_values['bankAccountNo']) ||
              empty($section_values['bankBranchNo'])
            )
          ) {
            $form_state->setError($element, t('The IBAN has to be set.'));
          }

          // validate check the field
          if (!empty($element['#value']) && !self::isValidIBAN($element['#value'])) {
            $form_state->setError($element, t('The IBAN is not a valid.'));
          }

        }
        break;

      case "swiftCode" :
        if ($section_values['paymentMethod'] == 1) {

          $field_settings = $form_state->getFormObject()->getEntity()
            ->getFieldDefinition(preg_replace('/\[[^\]]*\]/', '', $element['#name']))
            ->getSetting('forms_suite_configs');

          // check if the field has to be set
          if (
            empty($section_values['swiftCode']) &&
            !empty($section_values['IBAN']) &&
            (
              empty($section_values['bankAccountNo']) ||
              empty($section_values['bankBranchNo'])
            ) && empty($field_settings['optional_bic'])
          ) {
            $form_state->setError($element, t('The BIC has to be set.'));
          }

          // validate check the field
          if ($section_values['paymentMethod'] == 1) {
            if (!empty($element['#value']) && !self::isValidBIC($element['#value'])) {
              $form_state->setError($element, t('The BIC is not a valid.'));
            }
          }
        }
        break;
      case 'bankAccountNo':

        // check if the field has to be set
        if (
          empty($section_values['bankAccountNo']) &&
          !empty($section_values['bankBranchNo']) &&
          (
            empty($section_values['IBAN']) ||
            empty($section_values['swiftCode'])
          )
        ) {
          $form_state->setError($element, t('The bank account number has to be set.'));
        }

        // check if bank account number is numeric if it is filled.
        if ($section_values['paymentMethod'] == 1) {
          if (!empty($element['#value']) && !is_numeric($element['#value'])) {
            $form_state->setError($element, t('The bank account number is not a valid.'));
          }
        }
        break;
      case 'bankBranchNo':

        // check if the field has to be set
        if (
          empty($section_values['bankBranchNo']) &&
          !empty($section_values['bankAccountNo']) &&
          (
            empty($section_values['IBAN']) ||
            empty($section_values['swiftCode'])
          )
        ) {
          $form_state->setError($element, t('The bank branch number has to be set.'));
        }

        // check if bank branch number is numeric if it is filled.
        if ($section_values['paymentMethod'] == 1) {
          if (!empty($element['#value']) && !is_numeric($element['#value'])) {
            $form_state->setError($element, t('The bank branch number is not a valid.'));
          }
        }
        break;
    }

    parent::validateElement($element, $form_state);

  }

  /**
   * Validates a IBAN (International Bank Acount Number)
   *
   * @param $iban
   *  The iban to validate.
   * @return bool
   *  TRUE if the bic is valid otherwise FALSE.
   */
  public static function isValidIBAN($iban)
  {
    $iban = strtolower($iban);
    $Countries = array(
      'al' => 28, 'ad' => 24, 'at' => 20, 'az' => 28, 'bh' => 22, 'be' => 16, 'ba' => 20, 'br' => 29, 'bg' => 22, 'cr' => 21, 'hr' => 21, 'cy' => 28, 'cz' => 24,
      'dk' => 18, 'do' => 28, 'ee' => 20, 'fo' => 18, 'fi' => 18, 'fr' => 27, 'ge' => 22, 'de' => 22, 'gi' => 23, 'gr' => 27, 'gl' => 18, 'gt' => 28, 'hu' => 28,
      'is' => 26, 'ie' => 22, 'il' => 23, 'it' => 27, 'jo' => 30, 'kz' => 20, 'kw' => 30, 'lv' => 21, 'lb' => 28, 'li' => 21, 'lt' => 20, 'lu' => 20, 'mk' => 19,
      'mt' => 31, 'mr' => 27, 'mu' => 30, 'mc' => 27, 'md' => 24, 'me' => 22, 'nl' => 18, 'no' => 15, 'pk' => 24, 'ps' => 29, 'pl' => 28, 'pt' => 25, 'qa' => 29,
      'ro' => 24, 'sm' => 27, 'sa' => 24, 'rs' => 22, 'sk' => 24, 'si' => 19, 'es' => 24, 'se' => 24, 'ch' => 21, 'tn' => 24, 'tr' => 26, 'ae' => 23, 'gb' => 22, 'vg' => 24
    );
    $Chars = array(
      'a' => 10, 'b' => 11, 'c' => 12, 'd' => 13, 'e' => 14, 'f' => 15, 'g' => 16, 'h' => 17, 'i' => 18, 'j' => 19, 'k' => 20, 'l' => 21, 'm' => 22,
      'n' => 23, 'o' => 24, 'p' => 25, 'q' => 26, 'r' => 27, 's' => 28, 't' => 29, 'u' => 30, 'v' => 31, 'w' => 32, 'x' => 33, 'y' => 34, 'z' => 35
    );

    if (strlen($iban) != $Countries[substr($iban, 0, 2)]) {
      return false;
    }

    $MovedChar = substr($iban, 4) . substr($iban, 0, 4);
    $MovedCharArray = str_split($MovedChar);
    $NewString = "";

    foreach ($MovedCharArray as $k => $v) {

      if (!is_numeric($MovedCharArray[$k])) {
        $MovedCharArray[$k] = $Chars[$MovedCharArray[$k]];
      }
      $NewString .= $MovedCharArray[$k];
    }
    if (function_exists("bcmod")) {
      return bcmod($NewString, '97') == 1;
    }

    $x = $NewString;
    $y = "97";
    $take = 5;
    $mod = "";

    do {
      $a = (int)$mod . substr($x, 0, $take);
      $x = substr($x, $take);
      $mod = $a % $y;
    } while (strlen($x));

    return (int)$mod == 1;
  }

  /**
   * Validates a BIC (Business Identifier Code)
   *
   * @param $bic
   *  The bic to validate.
   * @return bool
   *  TRUE if the bic is valid otherwise FALSE.
   */
  public static function isValidBIC($bic)
  {
    return preg_match('/^([a-zA-Z]){4}([a-zA-Z]){2}([0-9a-zA-Z]){2}([0-9a-zA-Z]{3})?$/', strtolower(trim($bic)));
  }

}
