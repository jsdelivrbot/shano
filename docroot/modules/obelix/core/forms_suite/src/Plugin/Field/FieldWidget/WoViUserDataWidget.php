<?php

namespace Drupal\forms_suite\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'wovi_user_data_widget' widget.
 *
 * @FieldWidget(
 *   id = "wovi_user_data_widget",
 *   multiple_values = TRUE,
 *   label = @Translation("Forms suite user data widget"),
 *   field_types = {
 *     "wovi_user_data_field"
 *   }
 * )
 */
class WoViUserDataWidget extends FormsSuiteWidget
{

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'hide_initials' => TRUE,
      'hide_middle_name' => TRUE,
      'hide_first_name' => FALSE,
      'hide_country' => FALSE,
      'hide_title' => FALSE,
      'hide_birthday' => FALSE,
      'optional_bic' => FALSE,
      'postal_max_length' => 5,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $element['hide_initials'] = array(
      '#type' => 'checkbox',
      '#title' => t('Hide initials'),
      '#default_value' => $this->getSetting('hide_initials'),
    );

    $element['hide_middle_name'] = array(
      '#type' => 'checkbox',
      '#title' => t('Hide middle name'),
      '#default_value' => $this->getSetting('hide_middle_name'),
    );

    $element['hide_first_name'] = array(
      '#type' => 'checkbox',
      '#title' => t('Hide first name'),
      '#default_value' => $this->getSetting('hide_first_name'),
    );

    $element['hide_country'] = array(
      '#type' => 'checkbox',
      '#title' => t('Hide country'),
      '#default_value' => $this->getSetting('hide_country'),
    );

    $element['hide_birthday'] = array(
      '#type' => 'checkbox',
      '#title' => t('Hide birthday'),
      '#default_value' => $this->getSetting('hide_birthday'),
    );

    $element['hide_title'] = array(
      '#type' => 'checkbox',
      '#title' => t('Hide title'),
      '#default_value' => $this->getSetting('hide_title'),
    );

    $element['postal_max_length'] = array(
      '#type' => 'textfield',
      '#title' => t('Postal code max length'),
      '#default_value' => $this->getSetting('postal_max_length'),
    );

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state)
  {

    $element = parent::formElement($items, $delta, $element, $form, $form_state);

    $salutation_title = t('Salutation');
    $salutation_options = [
      1 => t('Mr.'),
      2 => t('Mrs.'),
      3 => t('Family'),
      4 => t('Mr. and Mrs.'),
      100 => t('Company'),
    ];
    if (forms_suite_is_child_sponsorship_english_form()) {
      $salutation_title = 'Form of address';
      $salutation_options = [
        1 => 'Mr.',
        2 => 'Mrs.',
        3 => 'Family',
        4 => 'Mr. and Mrs.',
        100 => 'Company',
      ];
    }
    $element['salutation'] = [
      '#type' => 'select',
      '#title' => $salutation_title,
      '#default_value' => isset($items[$delta]->value) ? $items[$delta]->value : 1,
      '#options' => $salutation_options,
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
      '#required' => TRUE,
    ];

    $job_title_code_title = t('Title');
    if (forms_suite_is_child_sponsorship_english_form()) {
      $job_title_code_title = 'Title';
    }
    $element['jobTitleCode'] = [
      '#type' => 'select',
      '#title' => $job_title_code_title,
      '#default_value' => isset($items[$delta]->value) ? $items[$delta]->value : 0,
      '#options' => [
        '' => t('(optional)'),
        1 => t('Dr.'),
        14 => t('Prof.'),
      ],
      '#validated' => $this->getSetting('hide_title'),
      '#access' => !$this->getSetting('hide_title'),
      '#element_validate' => [[get_class($this), 'validateElement']],
    ];

    $element['initials'] = [
      '#type' => 'textfield',
      '#title' => t('Initials'),
      '#maxlength' => $this->getFieldSetting('max_length'),
      '#ajax' => [
        'callback' => [$this, "validateFormAjax"],
        'event' => 'blur',
        'disable-refocus' => TRUE,
        'progress' => [
          'type' => 'throbber',
          'message' => t('Validating'),
        ],
      ],
      '#validated' => $this->getSetting('hide_initials'),
      '#access' => !$this->getSetting('hide_initials'),
      '#element_validate' => [[get_class($this), 'validateElement']],
      '#required' => TRUE,
    ];

    $first_name_title = t('First name');
    if (forms_suite_is_child_sponsorship_english_form()) {
      $first_name_title = 'First name';
    }
    $element['firstName'] = [
      '#type' => 'textfield',
      '#title' => $first_name_title,
      '#maxlength' => 30,
      '#ajax' => [
        'callback' => [$this, "validateFormAjax"],
        'event' => 'blur',
        'disable-refocus' => TRUE,
        'progress' => [
          'type' => 'throbber',
          'message' => t('Validating'),
        ],
      ],
      '#validated' => $this->getSetting('hide_first_name'),
      '#access' => !$this->getSetting('hide_first_name'),
      '#element_validate' => [[get_class($this), 'validateElement']],
      '#required' => TRUE,
    ];

    $element['middleName'] = [
      '#type' => 'textfield',
      '#title' => t('Middle name'),
      '#maxlength' => 30,
      '#ajax' => [
        'callback' => [$this, "validateFormAjax"],
        'event' => 'blur',
        'disable-refocus' => TRUE,
        'progress' => [
          'type' => 'throbber',
          'message' => t('Validating'),
        ],
      ],
      '#validated' => $this->getSetting('hide_middle_name'),
      '#access' => !$this->getSetting('hide_middle_name'),
      '#element_validate' => [[get_class($this), 'validateElement']],
    ];

    $surname_title = t('Surname');
    if (forms_suite_is_child_sponsorship_english_form()) {
      $surname_title = 'Last name';
    }
    $element['surname'] = [
      '#type' => 'textfield',
      '#title' => $surname_title,
      '#maxlength' => 30,
      '#ajax' => [
        'callback' => [get_class($this), 'validateFormAjax'],
        'event' => 'blur',
        'disable-refocus' => TRUE,
        'progress' => [
          'type' => 'throbber',
          'message' => t('Validating'),
        ],
      ],
      '#element_validate' => [[get_class($this), 'validateElement']],
      '#required' => TRUE,
    ];

    $comapny_name_title = t('Company name');
    if (forms_suite_is_child_sponsorship_english_form()) {
      $comapny_name_title = 'Company name (optional)';
    }
    $element['companyName'] = [
      '#type' => 'textfield',
      '#title' => $comapny_name_title,
      '#maxlength' => 50,
      '#ajax' => [
        'callback' => [get_class($this), 'validateFormAjax'],
        'event' => 'blur',
        'disable-refocus' => TRUE,
        'progress' => [
          'type' => 'throbber',
          'message' => t('Validating'),
        ],
      ],
      '#element_validate' => [[get_class($this), 'validateElement']],
    ];

    $street_title = t('Street');
    if (forms_suite_is_child_sponsorship_english_form()) {
      $street_title = 'Street address';
    }
    $element['street'] = [
      '#type' => 'textfield',
      '#title' => $street_title,
      '#maxlength' => 40,
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
      '#required' => TRUE,
    ];

    $house_no_title = t('House number');
    if (forms_suite_is_child_sponsorship_english_form()) {
      $house_no_title = 'No.';
    }
    $element['houseNo'] = [
      '#type' => 'textfield',
      '#title' => $house_no_title,
      '#maxlength' => 10,
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
      '#required' => TRUE,
    ];

    $post_code_title = t('Post code');
    if (forms_suite_is_child_sponsorship_english_form()) {
      $post_code_title = 'ZIP code';
    }
    $element['postCode'] = [
      '#type' => 'textfield',
      '#title' => $post_code_title,
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
      '#required' => TRUE,
    ];

    $city_title = t('City');
    if (forms_suite_is_child_sponsorship_english_form()) {
      $city_title = 'City';
    }
    $element['city'] = [
      '#type' => 'textfield',
      '#title' => $city_title,
      '#maxlength' => 30,
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
      '#required' => TRUE,
    ];
    $country_title = t('Country');
    $country_list = self::getAllCountryNamesInEnglish();
    if (forms_suite_is_child_sponsorship_english_form()) {
      $country_title = 'Country';
      $country_list = self::getAllCountryNamesInEnglish();
    }
    $element['countryCode'] = [
      '#type' => 'select',
      '#title' => $country_title,
      '#maxlength' => $this->getFieldSetting('max_length'),
      '#options' => $country_list,
      '#validated' => $this->getSetting('hide_country'),
      '#access' => !$this->getSetting('hide_country'),
      '#element_validate' => [[get_class($this), 'validateElement']],
    ];

    $phone_title = t('Phone private (optional, for any questions)');
    if (forms_suite_is_child_sponsorship_english_form()) {
      $phone_title = 'Phone (optional, to check back)';
    }
    $element['phonePrivate'] = [
      '#type' => 'textfield',
      '#maxlength' => 30,
      '#title' => $phone_title,
    ];

    $email_title = t('E-Mail');
    if (forms_suite_is_child_sponsorship_english_form()) {
      $email_title = 'Email';
    }
    $element['email'] = [
      '#type' => 'email',
      '#title' => $email_title,
      '#maxlength' => 80,
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
      '#required' => TRUE,
    ];

    $birthday_option = t('Birthday');
    if (forms_suite_is_child_sponsorship_english_form()) {
      $birthday_option = 'Date of birth';
    }

    $display_birthday_markup = t('Can we see your <strong>birthday</strong> date?');
    if (forms_suite_is_child_sponsorship_english_form()) {
      $display_birthday_markup = 'May we surprise you on your <strong>birthday</strong>?';
    }
    $element['display_birthday'] = [
      '#markup' => $display_birthday_markup,
    ];

    $element['birthday'] = [
      '#type' => 'select',
      '#label_display' => 'invisible',
      '#default_value' => isset($items[$delta]->value) ? $items[$delta]->value : 0,
      '#options' => [
          0 => $birthday_option,
        ] + self::getBirthdayOptions(),
      '#validated' => $this->getSetting('hide_birthday'),
      '#access' => !$this->getSetting('hide_birthday'),
      '#element_validate' => [[get_class($this), 'validateElement']],
    ];

    $birthmonth_option = t('Birthmonth');
    if (forms_suite_is_child_sponsorship_english_form()) {
      $birthmonth_option = 'Month';
    }
    $element['birthmonth'] = [
      '#type' => 'select',
      '#label_display' => 'invisible',
      '#default_value' => isset($items[$delta]->value) ? $items[$delta]->value : 0,
      '#options' => [
          0 => $birthmonth_option,
        ] + self::getBirthmonthOptions(),
      '#validated' => $this->getSetting('hide_birthday'),
      '#access' => !$this->getSetting('hide_birthday'),
      '#element_validate' => [[get_class($this), 'validateElement']],
    ];

    $birthyear_option = t('Birthyear');
    if (forms_suite_is_child_sponsorship_english_form()) {
      $birthyear_option = 'Year';
    }
    $element['birthyear'] = [
      '#type' => 'select',
      '#label_display' => 'invisible',
      '#default_value' => isset($items[$delta]->value) ? $items[$delta]->value : 0,
      '#options' => [
          0 => $birthyear_option,
        ] + self::getBirthyearOptions(),
      '#validated' => $this->getSetting('hide_birthday'),
      '#access' => !$this->getSetting('hide_birthday'),
      '#element_validate' => [[get_class($this), 'validateElement']],
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public static function validateElement(array $element, FormStateInterface &$form_state)
  {
    $form_state->setLimitValidationErrors(NULL);
    $section_values = $form_state->getValues();
    $field_user_data = $section_values['field_user_data'];

    if ($element['#required'] && $element['#value'] == '') {
      $form_state->setError($element, t('@name field is required.', array('@name' => $element['#title'])));
    }

    if (isset($element['#maxlength']) && $element['#maxlength'] && strlen($element['#value']) >= $element['#maxlength']) {
      $form_state->setError($element, t('@name field is to long.', array('@name' => $element['#title'])));
    }

    switch (end($element['#parents'])) {
      case "email" :
        if (!\Drupal::service('email.validator')->isValid($element['#value'])) {
          $form_state->setError($element, t('The e-mail is not a valid.'));
        }
        break;
      case 'postCode':
        try {
          $form_display = $form_state->get('form_display');
          $user_data_settings = $form_display->get('content')['field_user_data']['settings'];

          $postal_max_length = !empty($user_data_settings['postal_max_length'])
            ? $user_data_settings['postal_max_length'] : 5;
          if (forms_suite_is_child_sponsorship_english_form()) {
            $postal_max_length = 20;
          }
          // If by some reason the value is not available on legacy sites use legacy value by default.
        } catch (\Exception $e) {
          $postal_max_length = 5;
        }

        if (!empty($element['#value']) && strlen($element['#value']) > $postal_max_length){
          $form_state->setError(
            $element,
            t(
              '@name could have only @max chars.',
              array('@name' => $element['#title'], '@max' => $postal_max_length)
            )
          );
        }
        break;

      case 'birthday':
      case 'birthmonth':
      case 'birthyear':
        // check if all birthday fields are set and if the date is valid.
        if (!empty($element['#value'])) {
          if (empty($field_user_data['birthday']) || empty($field_user_data['birthmonth']) || empty($field_user_data['birthyear'])) {
            $form_state->setError($element, t('All birthday fields have to be set.'));
          } elseif (!checkdate($field_user_data['birthmonth'], $field_user_data['birthday'], $field_user_data['birthyear'])) {
            $form_state->setError($element, t('Birthday is not valid.'));
          }
        }
        break;
    }

    parent::validateElement($element, $form_state);
  }

  private static function getBirthdayOptions()
  {
    $result = [];
    for ($i = 1; $i <= 31; $i++) {
      $i = str_pad($i, 2, 0, STR_PAD_LEFT);
      $result[$i] = $i;
    }
    return $result;
  }

  private static function getBirthmonthOptions()
  {
    $result = [];
    for ($i = 1; $i <= 12; $i++) {
      $i = str_pad($i, 2, 0, STR_PAD_LEFT);
      $result[$i] = $i;
    }
    return $result;
  }

  private static function getBirthyearOptions()
  {
    $result = [];
    for ($i = date('Y'); $i >= date('Y', strtotime('-110 years')); $i--) {
      $result[$i] = $i;
    }
    return $result;
  }

  public static function getAllCountryNames()
  {
    return [
      'DE' => t('Deutschland'),
      'AT' => t('Österreich'),
      'CH' => t('Schweiz'),
      'AF' => t('Afghanistan'),
      'EG' => t('Ägypten'),
      'AX' => t('Aland'),
      'AL' => t('Albanien'),
      'DZ' => t('Algerien'),
      'AS' => t('Amerikanisch-Samoa'),
      'VI' => t('Amerikanische Jungferninseln'),
      'AD' => t('Andorra'),
      'AO' => t('Angola'),
      'AI' => t('Anguilla'),
      'AQ' => t('Antarktis'),
      'AG' => t('Antigua und Barbuda'),
      'GQ' => t('Äquatorialguinea'),
      'AR' => t('Argentinien'),
      'AM' => t('Armenien'),
      'AW' => t('Aruba'),
      'AC' => t('Ascension'),
      'AZ' => t('Aserbaidschan'),
      'ET' => t('Äthiopien'),
      'AU' => t('Australien'),
      'BS' => t('Bahamas'),
      'BH' => t('Bahrain'),
      'BD' => t('Bangladesch'),
      'BB' => t('Barbados'),
      'BE' => t('Belgien'),
      'BZ' => t('Belize'),
      'BJ' => t('Benin'),
      'BM' => t('Bermuda'),
      'BT' => t('Bhutan'),
      'BO' => t('Bolivien'),
      'BA' => t('Bosnien und Herzegowina'),
      'BW' => t('Botswana'),
      'BV' => t('Bouvetinsel'),
      'BR' => t('Brasilien'),
      'BN' => t('Brunei'),
      'BG' => t('Bulgarien'),
      'BF' => t('Burkina Faso'),
      'BI' => t('Burundi'),
      'CL' => t('Chile'),
      'CN' => t('China'),
      'CK' => t('Cookinseln'),
      'CR' => t('Costa Rica'),
      'CI' => t("C'ote d'Ivoire"),
      'DK' => t('Dänemark'),
      'DG' => t('Diego Garcia'),
      'DM' => t('Dominica'),
      'DO' => t('Dominikanische Republik'),
      'DJ' => t('Dschibuti'),
      'EC' => t('Ecuador'),
      'SV' => t('El Salvador'),
      'ER' => t('Eritrea'),
      'EE' => t('Estland'),
      'EU' => t('Europäische Union'),
      'FK' => t('Falklandinseln'),
      'FO' => t('Färöer'),
      'FJ' => t('Fidschi'),
      'FI' => t('Finnland'),
      'FR' => t('Frankreich'),
      'GF' => t('Französisch-Guayana'),
      'PF' => t('Französisch-Polynesien'),
      'GA' => t('Gabun'),
      'GM' => t('Gambia'),
      'GE' => t('Georgien'),
      'GH' => t('Ghana'),
      'GI' => t('Gibraltar'),
      'GD' => t('Grenada'),
      'GR' => t('Griechenland'),
      'GL' => t('Grönland'),
      'GB' => t('Großbritannien'),
      'CP' => t('Guadeloupe'),
      'GU' => t('Guam'),
      'GT' => t('Guatemala'),
      'GG' => t('Guernsey'),
      'GN' => t('Guinea'),
      'GW' => t('Guinea-Bissau'),
      'GY' => t('Guyana'),
      'HT' => t('Haiti'),
      'HM' => t('Heard und McDonaldinseln'),
      'HN' => t('Honduras'),
      'HK' => t('Hongkong'),
      'IN' => t('Indien'),
      'ID' => t('Indonesien'),
      'IQ' => t('Irak'),
      'IR' => t('Iran'),
      'IE' => t('Irland'),
      'IS' => t('Island'),
      'IL' => t('Israel'),
      'IT' => t('Italien'),
      'JM' => t('Jamaika'),
      'JP' => t('Japan'),
      'YE' => t('Jemen'),
      'JE' => t('Jersey'),
      'JO' => t('Jordanien'),
      'KY' => t('Kaimaninseln'),
      'KH' => t('Kambodscha'),
      'CM' => t('Kamerun'),
      'CA' => t('Kanada'),
      'IC' => t('Kanarische Inseln'),
      'CV' => t('Kap Verde'),
      'KZ' => t('Kasachstan'),
      'QA' => t('Katar'),
      'KE' => t('Kenia'),
      'KG' => t('Kirgisistan'),
      'KI' => t('Kiribati'),
      'CC' => t('Kokosinseln'),
      'CO' => t('Kolumbien'),
      'KM' => t('Komoren'),
      'CG' => t('Kongo'),
      'HR' => t('Kroatien'),
      'CU' => t('Kuba'),
      'KW' => t('Kuwait'),
      'LA' => t('Laos'),
      'LS' => t('Lesotho'),
      'LV' => t('Lettland'),
      'LB' => t('Libanon'),
      'LR' => t('Liberia'),
      'LY' => t('Libyen'),
      'LI' => t('Liechtenstein'),
      'LT' => t('Litauen'),
      'LU' => t('Luxemburg'),
      'MO' => t('Macao'),
      'MG' => t('Madagaskar'),
      'MW' => t('Malawi'),
      'MY' => t('Malaysia'),
      'MV' => t('Malediven'),
      'ML' => t('Mali'),
      'MT' => t('Malta'),
      'MA' => t('Marokko'),
      'MH' => t('Marshallinseln'),
      'MQ' => t('Martinique'),
      'MR' => t('Mauretanien'),
      'MU' => t('Mauritius'),
      'YT' => t('Mayotte'),
      'MK' => t('Mazedonien'),
      'MX' => t('Mexiko'),
      'FM' => t('Mikronesien'),
      'MD' => t('Moldawien'),
      'MC' => t('Monaco'),
      'MN' => t('Mongolei'),
      'ME' => t('Montenegro'),
      'MS' => t('Montserrat'),
      'MZ' => t('Mosambik'),
      'MM' => t('Myanmar'),
      'NA' => t('Namibia'),
      'NR' => t('Nauru'),
      'NP' => t('Nepal'),
      'NC' => t('Neukaledonien'),
      'NZ' => t('Neuseeland'),
      'NT' => t('Neutrale Zone'),
      'NI' => t('Nicaragua'),
      'NL' => t('Niederlande'),
      'AN' => t('Niederländische Antillen'),
      'NE' => t('Niger'),
      'NG' => t('Nigeria'),
      'NU' => t('Niue'),
      'KP' => t('Nordkorea'),
      'MP' => t('Nördliche Marianen'),
      'NF' => t('Norfolkinsel'),
      'NO' => t('Norwegen'),
      'OM' => t('Oman'),
      'PK' => t('Pakistan'),
      'PS' => t('Palästina'),
      'PW' => t('Palau'),
      'PA' => t('Panama'),
      'PG' => t('Papua-Neuguinea'),
      'PY' => t('Paraguay'),
      'PE' => t('Peru'),
      'PH' => t('Philippinen'),
      'PN' => t('Pitcairninseln'),
      'PL' => t('Polen'),
      'PT' => t('Portugal'),
      'PR' => t('Puerto Rico'),
      'RE' => t('Réunion'),
      'RW' => t('Ruanda'),
      'RO' => t('Rumänien'),
      'RU' => t('Russische Föderation'),
      'SB' => t('Salomonen'),
      'ZM' => t('Sambia'),
      'WS' => t('Samoa'),
      'SM' => t('San Marino'),
      'ST' => t('São Tomé und Príncipe'),
      'SA' => t('Saudi-Arabien'),
      'SE' => t('Schweden'),
      'SN' => t('Senegal'),
      'RS' => t('Serbien'),
      'SC' => t('Seychellen'),
      'SL' => t('Sierra Leone'),
      'ZW' => t('Simbabwe'),
      'SG' => t('Singapur'),
      'SK' => t('Slowakei'),
      'SI' => t('Slowenien'),
      'SO' => t('Somalia'),
      'ES' => t('Spanien'),
      'LK' => t('Sri Lanka'),
      'SH' => t('St. Helena'),
      'KN' => t('St. Kitts und Nevis'),
      'LC' => t('St. Lucia'),
      'PM' => t('St. Pierre und Miquelon'),
      'VC' => t('St. Vincent/Grenadinen (GB)'),
      'ZA' => t('Südafrika, Republik'),
      'SD' => t('Sudan'),
      'KR' => t('Südkorea'),
      'SR' => t('Suriname'),
      'SJ' => t('Svalbard und Jan Mayen'),
      'SZ' => t('Swasiland'),
      'SY' => t('Syrien'),
      'TJ' => t('Tadschikistan'),
      'TW' => t('Taiwan'),
      'TZ' => t('Tansania'),
      'TH' => t('Thailand'),
      'TL' => t('Timor-Leste'),
      'TG' => t('Togo'),
      'TK' => t('Tokelau'),
      'TO' => t('Tonga'),
      'TT' => t('Trinidad und Tobago'),
      'TA' => t('Tristan da Cunha'),
      'TD' => t('Tschad'),
      'CZ' => t('Tschechische Republik'),
      'TN' => t('Tunesien'),
      'TR' => t('Türkei'),
      'TM' => t('Turkmenistan'),
      'TC' => t('Turks- und Caicosinseln'),
      'TV' => t('Tuvalu'),
      'UG' => t('Uganda'),
      'UA' => t('Ukraine'),
      'HU' => t('Ungarn'),
      'UY' => t('Uruguay'),
      'UZ' => t('Usbekistan'),
      'VU' => t('Vanuatu'),
      'VA' => t('Vatikanstadt'),
      'VE' => t('Venezuela'),
      'AE' => t('Vereinigte Arabische Emirate'),
      'US' => t('Vereinigte Staaten von Amerika'),
      'VN' => t('Vietnam'),
      'WF' => t('Wallis und Futuna'),
      'CX' => t('Weihnachtsinsel'),
      'BY' => t('Weißrussland'),
      'EH' => t('Westsahara'),
      'CF' => t('Zentralafrikanische Republik'),
      'CY' => t('Zypern'),
    ];
  }
  public static function getAllCountryNamesInEnglish()
  {
    $countries = [
      'DE' => 'Germany',
      'AT' => 'Austria',
      'CH' => 'Switzerland',
      'AF' => 'Afghanistan',
      'EG' => 'Egypt',
      'AX' => 'Aland',
      'AL' => 'Albania',
      'DZ' => 'Algeria',
      'AS' => 'American Samoa',
      'VI' => 'American Virgin Island',
      'AD' => 'Andorra',
      'AO' => 'Angola',
      'AI' => 'Anguilla',
      'AQ' => 'Antarctica',
      'AG' => 'Antigua and Barbuda',
      'GQ' => 'Equatorial Guinea',
      'AR' => 'Argentina',
      'AM' => 'Armenia',
      'AW' => 'Aruba',
      'AC' => 'Ascension',
      'AZ' => 'Azerbaijan',
      'ET' => 'Ethiopia',
      'AU' => 'Australia',
      'BS' => 'Bahamas',
      'BH' => 'Bahrain',
      'BD' => 'Bangladesh',
      'BB' => 'Barbados',
      'BE' => 'Belgium',
      'BZ' => 'Belize',
      'BJ' => 'Benin',
      'BM' => 'Bermuda',
      'BT' => 'Bhutan',
      'BO' => 'Bolivia',
      'BA' => 'Bosnia and Herzegovina',
      'BW' => 'Botswana',
      'BV' => 'Bouvet Island',
      'BR' => 'Brazil',
      'BN' => 'Brunei Darussalem',
      'BG' => 'Bulgaria',
      'BF' => 'Burkina Faso',
      'BI' => 'Burundi',
      'CL' => 'Chile',
      'CN' => 'China',
      'CK' => 'Cook Islands',
      'CR' => 'Costa Rica',
      'CI' => 'Ivory Coast',
      'DK' => 'Denmark',
      'DG' => 'Diego Garcia',
      'DM' => 'Dominica',
      'DO' => 'Dominikanische Republik',
      'DJ' => 'Djibouti',
      'EC' => 'Ecuador',
      'SV' => 'El Salvador',
      'ER' => 'Eritrea',
      'EE' => 'Estonia',
      'EU' => 'European Union',
      'FK' => 'Falkland Islands',
      'FO' => 'Faroe Islands',
      'FJ' => 'Fiji',
      'FI' => 'Finland',
      'FR' => 'France',
      'GF' => 'French Guiana',
      'PF' => 'French Polynesia',
      'GA' => 'Gabon',
      'GM' => 'Gambia',
      'GE' => 'Georgia',
      'GH' => 'Ghana',
      'GI' => 'Gibraltar',
      'GD' => 'Grenada',
      'GR' => 'Greece',
      'GL' => 'Greenland',
      'GB' => 'Great Britain',
      'CP' => 'Guadeloupe',
      'GU' => 'Guam',
      'GT' => 'Guatemala',
      'GG' => 'Guernsey',
      'GN' => 'Guinea',
      'GW' => 'Guinea-Bissau',
      'GY' => 'Guyana',
      'HT' => 'Haiti',
      'HM' => 'Heard and Mc Donald Islands',
      'HN' => 'Honduras',
      'HK' => 'Hong Kong',
      'IN' => 'India',
      'ID' => 'Indonesia',
      'IQ' => 'Iraq',
      'IR' => 'Iran',
      'IE' => 'Ireland',
      'IS' => 'Iceland',
      'IL' => 'Israel',
      'IT' => 'Italy',
      'JM' => 'Jamaica',
      'JP' => 'Japan',
      'YE' => 'Yemen',
      'JE' => 'Jersey',
      'JO' => 'Jordan',
      'KY' => 'Cayman Islands',
      'KH' => 'Cambodia',
      'CM' => 'Cameroon',
      'CA' => 'Canada',
      'IC' => 'Canary Islands',
      'CV' => 'Cape Verde',
      'KZ' => 'Kaszakhstan',
      'QA' => 'Qatar',
      'KE' => 'Kenya',
      'KG' => 'Kyrgyzstan',
      'KI' => 'Kiribati',
      'CC' => 'Cocos Islands',
      'CO' => 'Colombia',
      'KM' => 'Comoros',
      'CG' => 'Congo',
      'HR' => 'Croatia',
      'CU' => 'Kuba',
      'KW' => 'Kuweit',
      'LA' => 'Laos',
      'LS' => 'Lesotho',
      'LV' => 'Latvia',
      'LB' => 'Lebanon',
      'LR' => 'Liberia',
      'LY' => 'Libya',
      'LI' => 'Liechtenstein',
      'LT' => 'Lithuania',
      'LU' => 'Luxembourg',
      'MO' => 'Macao',
      'MG' => 'Madagascar',
      'MW' => 'Malawi',
      'MY' => 'Malaysia',
      'MV' => 'Maldives',
      'ML' => 'Mali',
      'MT' => 'Malta',
      'MA' => 'Morocco',
      'MH' => 'Marshall Islands',
      'MQ' => 'Martinique',
      'MR' => 'Mauritania',
      'MU' => 'Mauritius',
      'YT' => 'Mayotte',
      'MK' => 'Macedonia, Rep. Of',
      'MX' => 'Mexico',
      'FM' => 'Micronesia, Federal States of',
      'MD' => 'Moldawien',
      'MC' => 'Monaco',
      'MN' => 'Mongolia',
      'ME' => 'Montenegro',
      'MS' => 'Montserrat',
      'MZ' => 'Mozambique',
      'MM' => 'Myanmar',
      'NA' => 'Namibia',
      'NR' => 'Nauru',
      'NP' => 'Nepal',
      'NC' => 'New Caledonia',
      'NZ' => 'New Zealand',
      'NT' => 'Neutral Zone',
      'NI' => 'Nicaragua',
      'NL' => 'Netherlands',
      'AN' => 'Netherlands Antilles',
      'NE' => 'Niger',
      'NG' => 'Nigeria',
      'NU' => 'Niue',
      'KP' => 'North Korea',
      'MP' => 'Northern Mariana Islands',
      'NF' => 'Norfolk Island',
      'NO' => 'Norway',
      'OM' => 'Oman',
      'PK' => 'Pakistan',
      'PS' => 'Palestine',
      'PW' => 'Palau',
      'PA' => 'Panama',
      'PG' => 'Papua New Guinea',
      'PY' => 'Paraguay',
      'PE' => 'Peru',
      'PH' => 'Philippines',
      'PN' => 'Pitcairn Island',
      'PL' => 'Poland',
      'PT' => 'Portugal',
      'PR' => 'Puerto Rico',
      'RE' => 'Reunion Island',
      'RW' => 'Rwanda',
      'RO' => 'Romania',
      'RU' => 'Russion Federation',
      'SB' => 'Solomon Islands',
      'ZM' => 'Zambia',
      'WS' => 'Samoa',
      'SM' => 'San Marino',
      'ST' => 'Sao Tome und Príncipe',
      'SA' => 'Saudi Arabia',
      'SE' => 'Sweden',
      'SN' => 'Senegal',
      'RS' => 'Serbia',
      'SC' => 'Seychelles',
      'SL' => 'Sierra Leone',
      'ZW' => 'Zimbabwe',
      'SG' => 'Singapore',
      'SK' => 'Slovakia',
      'SI' => 'Slovenia',
      'SO' => 'Somalia',
      'ES' => 'Spain',
      'LK' => 'Sri Lanka',
      'SH' => 'Saint Helena',
      'KN' => 'St. Kitts und Nevis',
      'LC' => 'St. Lucia',
      'PM' => 'St. Pierre and Miquelon',
      'VC' => 'St. Vincent/Grenadinen (GB)',
      'ZA' => 'South Africa',
      'SD' => 'Sudan',
      'KR' => 'South Korea',
      'SR' => 'Suriname',
      'SJ' => 'Svalbard and Jan Mayen Islands',
      'SZ' => 'Swaziland',
      'SY' => 'Syria',
      'TJ' => 'Tajikistan',
      'TW' => 'Taiwan, Republic of China',
      'TZ' => 'Tanzania',
      'TH' => 'Thailand',
      'TL' => 'East Timor',
      'TG' => 'Togor',
      'TK' => 'Tokelau',
      'TO' => 'Tonga',
      'TT' => 'Trinidad and Tobago',
      'TA' => 'Tristan da Cunha',
      'TD' => 'Chad',
      'CZ' => 'Czech Republic',
      'TN' => 'Tunisia',
      'TR' => 'Turkey',
      'TM' => 'Turkmenistan',
      'TC' => 'Turks- and Caicos Islands',
      'TV' => 'Tuvalu',
      'UG' => 'Uganda',
      'UA' => 'Ukraine',
      'HU' => 'Hungary',
      'UY' => 'Uruguay',
      'UZ' => 'Uzbekistan',
      'VU' => 'Vanuatu',
      'VA' => 'Vatican',
      'VE' => 'Venezuela',
      'AE' => 'United Arab Emirates',
      'US' => 'USA',
      'VN' => 'Vietnam',
      'WF' => 'Wallis and Futuna Islands',
      'CX' => 'Christmas Island',
      'BY' => 'Belarus',
      'EH' => 'Western Sahara',
      'CF' => 'Central African Republic',
      'CY' => 'Cyprus',
    ];
    asort($countries);
    return $countries;
  }
}


