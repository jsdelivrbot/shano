<?php

namespace Drupal\child_sponsorship\Plugin\Field\FieldWidget;

use Drupal\child\Controller\ChildController;
use Drupal\country\Controller\CountryController;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\forms_suite\Plugin\Field\FieldWidget\FormsSuiteWidget;


/**
 * Plugin implementation of the 'wovi_child_select_widget' widget.
 *
 * @FieldWidget(
 *   id = "wovi_child_select_widget",
 *   multiple_values = TRUE,
 *   label = @Translation("Forms suite child select widget"),
 *   field_types = {
 *     "wovi_child_select_field"
 *   }
 * )
 */
class WoViChildSelectWidget extends FormsSuiteWidget
{
  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state)
  {

    $element = parent::formElement($items, $delta, $element, $form, $form_state);

    $child_gender_title = t('Child gender');
    $child_gender_option = t('World Vision decides.');
    if (forms_suite_is_child_sponsorship_english_form()) {
      $child_gender_title = 'Gender';
      $child_gender_option = 'World Vision chooses';
    }
    $element['childGender'] = [
      '#type' => 'select',
      '#title' => $child_gender_title,
      '#default_value' => isset($items[$delta]->value) ? $items[$delta]->value : 1,
      '#options' => [
          'default' => $child_gender_option,
        ] + ChildController::getGenderOptions(),
      '#element_validate' => [[get_class($this), 'validateElement']],
    ];


    $forms_suite_configs = $this->getFieldSetting('forms_suite_configs');
    if (isset($forms_suite_configs['all_countries']) && $forms_suite_configs['all_countries']) {
      $countries = CountryController::getCountryOptions();
    }else{
      $countries = CountryController::getCountryOptionsWithChildren();
    }

    $child_country_title = t('Child country');
    $child_country_option = t('Where it is most needed');
    if (forms_suite_is_child_sponsorship_english_form()) {
      $child_country_title = 'Country';
      $child_country_option = 'Wherever it is needed most';
      $country_list_english = $this->countriesListEnglish();
      $continent_list_in_english = $this->continentNamesFromGermanToEnglish();
      // Replace the name of the countries with their English names.
      foreach ($countries as $continent => &$country_data) {
        // Replace the continent name with his English name.
        if (!empty($continent_list_in_english[$continent])) {
          $continent_name_in_english = $continent_list_in_english[$continent];
          $countries[$continent_name_in_english] = $countries[$continent];
          unset($countries[$continent]);
        }
        foreach ($country_data as $country_code => &$country_name) {
          if (!empty($country_list_english[$country_code])) {
            $country_name = $country_list_english[$country_code];
          }
        }
        // Sort the countries in every continent.
        asort($country_data);
      }
      // Sort the countries.
      ksort($countries);
    }
    $element['childCountryCode'] = [
      '#type' => 'select',
      '#title' => $child_country_title,
      '#default_value' => isset($items[$delta]->value) ? $items[$delta]->value : 1,
      '#options' => [
          '' => $child_country_option,
        ] + $countries,
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

    $search_args = [];
    if (isset($section_values['field_child_select'])) {
      if (
        isset($section_values['field_child_select']['childGender']) &&
        $section_values['field_child_select']['childGender'] != 'default'
      ) {
        $search_args['gender'] = $section_values['field_child_select']['childGender'];
      }
      if (
        isset($section_values['field_child_select']['childCountryCode']) &&
        $section_values['field_child_select']['childCountryCode'] != ''
      ) {
        $search_args['country'] = $section_values['field_child_select']['childCountryCode'];
      }
    }


    switch (end($element['#parents'])) {
      case "childGender" :
        if (isset($section_values['field_suggestion'])) {

          if ($section_values['field_suggestion']['suggestion'] == 1) {
            // Let form user different user source data fields.
            switch (TRUE) {
              case !empty($section_values['field_user']['email']):
                $user_email = $section_values['field_user']['email'];
                break;

              case !empty($section_values['field_user_data']['email']):
                $user_email = $section_values['field_user_data']['email'];
                break;
            }

            if (!empty($user_email) && !ChildController::checkBlockedEmail($user_email)) {
              // get random Child
              $child_controller = new ChildController();
              $child = $child_controller->getRandomChild($search_args);
              if (!$child) {
                $form_state->setError($element, t('Could not find a matching child.'));

              } else {
                // @todo Has to be activated for live
                $child->blockChildInForm();
                $child_controller->setBlockedChildForUser($child);

                $form_state->setValue('field_suggestion', [
                  'suggestion' => 1,
                  'childSequenceNo' => $child->getUniqueSequenceNumber(),
                  'childCountryCode' => $child->getCountry()->getCountryCode(),
                ]);
              }
            } else {
              $form_state->setError($element, t('You have already received a child suggestion.'));
            }
          }
        }


        break;
    }

    parent::validateElement($element, $form_state);
  }

  public function countriesListEnglish() {
    return [
      'BGD' => 'Bangladesh',
      'BEL' => 'Belgium',
      'BFA' => 'Burkina Faso',
      'BGR' => 'Bulgaria',
      'BIH' => 'Bosnia and Herzegovina',
      'BRB' => 'Barbados',
      'WLF' => 'Wallis and Futuna',
      'BLM' => 'Saint Barthelemy',
      'BMU' => 'Bermuda',
      'BRN' => 'Brunei',
      'BOL' => 'Bolivia',
      'BHR' => 'Bahrain',
      'BDI' => 'Burundi',
      'BEN' => 'Benin',
      'BTN' => 'Bhutan',
      'JAM' => 'Jamaica',
      'BVT' => 'Bouvet Island',
      'BWA' => 'Botswana',
      'WSM' => 'Samoa',
      'BES' => 'Bonaire, Saint Eustatius and Saba ',
      'BRA' => 'Brazil',
      'BHS' => 'Bahamas',
      'JEY' => 'Jersey',
      'BLR' => 'Belarus',
      'BLZ' => 'Belize',
      'RUS' => 'Russia',
      'RWA' => 'Rwanda',
      'SRB' => 'Serbia',
      'TLS' => 'East Timor',
      'REU' => 'Reunion',
      'TKM' => 'Turkmenistan',
      'TJK' => 'Tajikistan',
      'ROU' => 'Romania',
      'TKL' => 'Tokelau',
      'GNB' => 'Guinea-Bissau',
      'GUM' => 'Guam',
      'GTM' => 'Guatemala',
      'SGS' => 'South Georgia and the South Sandwich Islands',
      'GRC' => 'Greece',
      'GNQ' => 'Equatorial Guinea',
      'GLP' => 'Guadeloupe',
      'JPN' => 'Japan',
      'GUY' => 'Guyana',
      'GGY' => 'Guernsey',
      'GUF' => 'French Guiana',
      'GEO' => 'Georgia',
      'GRD' => 'Grenada',
      'GBR' => 'United Kingdom',
      'GAB' => 'Gabon',
      'SLV' => 'El Salvador',
      'GIN' => 'Guinea',
      'GMB' => 'Gambia',
      'GRL' => 'Greenland',
      'GIB' => 'Gibraltar',
      'GHA' => 'Ghana',
      'OMN' => 'Oman',
      'TUN' => 'Tunisia',
      'JOR' => 'Jordan',
      'HRV' => 'Croatia',
      'HTI' => 'Haiti',
      'HUN' => 'Hungary',
      'HKG' => 'Hong Kong',
      'HND' => 'Honduras',
      'HMD' => 'Heard Island and McDonald Islands',
      'VEN' => 'Venezuela',
      'PRI' => 'Puerto Rico',
      'PSE' => 'Palestinian Territory',
      'PLW' => 'Palau',
      'PRT' => 'Portugal',
      'SJM' => 'Svalbard and Jan Mayen',
      'PRY' => 'Paraguay',
      'IRQ' => 'Iraq',
      'PAN' => 'Panama',
      'PYF' => 'French Polynesia',
      'PNG' => 'Papua New Guinea',
      'PER' => 'Peru',
      'PAK' => 'Pakistan',
      'PHL' => 'Philippines',
      'PCN' => 'Pitcairn',
      'POL' => 'Poland',
      'SPM' => 'Saint Pierre and Miquelon',
      'ZMB' => 'Zambia',
      'ESH' => 'Western Sahara',
      'EST' => 'Estonia',
      'EGY' => 'Egypt',
      'ZAF' => 'South Africa',
      'ECU' => 'Ecuador',
      'ITA' => 'Italy',
      'VNM' => 'Vietnam',
      'SLB' => 'Solomon Islands',
      'ETH' => 'Ethiopia',
      'SOM' => 'Somalia',
      'ZWE' => 'Zimbabwe',
      'SAU' => 'Saudi Arabia',
      'ESP' => 'Spain',
      'ERI' => 'Eritrea',
      'MNE' => 'Montenegro',
      'MDA' => 'Moldova',
      'MDG' => 'Madagascar',
      'MAF' => 'Saint Martin',
      'MAR' => 'Morocco',
      'MCO' => 'Monaco',
      'UZB' => 'Uzbekistan',
      'MMR' => 'Myanmar',
      'MLI' => 'Mali',
      'MAC' => 'Macao',
      'MNG' => 'Mongolia',
      'MHL' => 'Marshall Islands',
      'MKD' => 'Macedonia',
      'MUS' => 'Mauritius',
      'MLT' => 'Malta',
      'MWI' => 'Malawi',
      'MDV' => 'Maldives',
      'MTQ' => 'Martinique',
      'MNP' => 'Northern Mariana Islands',
      'MSR' => 'Montserrat',
      'MRT' => 'Mauritania',
      'IMN' => 'Isle of Man',
      'UGA' => 'Uganda',
      'TZA' => 'Tanzania',
      'MYS' => 'Malaysia',
      'MEX' => 'Mexico',
      'ISR' => 'Israel',
      'FRA' => 'France',
      'IOT' => 'British Indian Ocean Territory',
      'SHN' => 'Saint Helena',
      'FIN' => 'Finland',
      'FJI' => 'Fiji',
      'FLK' => 'Falkland Islands',
      'FSM' => 'Micronesia',
      'FRO' => 'Faroe Islands',
      'NIC' => 'Nicaragua',
      'NLD' => 'Netherlands',
      'NOR' => 'Norway',
      'NAM' => 'Namibia',
      'VUT' => 'Vanuatu',
      'NCL' => 'New Caledonia',
      'NER' => 'Niger',
      'NFK' => 'Norfolk Island',
      'NGA' => 'Nigeria',
      'NZL' => 'New Zealand',
      'NPL' => 'Nepal',
      'NRU' => 'Nauru',
      'NIU' => 'Niue',
      'COK' => 'Cook Islands',
      'XKX' => 'Kosovo',
      'CIV' => 'Ivory Coast',
      'CHE' => 'Switzerland',
      'COL' => 'Colombia',
      'CHN' => 'China',
      'CMR' => 'Cameroon',
      'CHL' => 'Chile',
      'CCK' => 'Cocos Islands',
      'CAN' => 'Canada',
      'COG' => 'Republic of the Congo',
      'CAF' => 'Central African Republic',
      'COD' => 'Democratic Republic of the Congo',
      'CZE' => 'Czech Republic',
      'CYP' => 'Cyprus',
      'CXR' => 'Christmas Island',
      'CRI' => 'Costa Rica',
      'CUW' => 'Curacao',
      'CPV' => 'Cape Verde',
      'CUB' => 'Cuba',
      'SWZ' => 'Swaziland',
      'SYR' => 'Syria',
      'SXM' => 'Sint Maarten',
      'KGZ' => 'Kyrgyzstan',
      'KEN' => 'Kenya',
      'SSD' => 'South Sudan',
      'SUR' => 'Suriname',
      'KIR' => 'Kiribati',
      'KHM' => 'Cambodia',
      'KNA' => 'Saint Kitts and Nevis',
      'COM' => 'Comoros',
      'STP' => 'Sao Tome and Principe',
      'SVK' => 'Slovakia',
      'KOR' => 'South Korea',
      'SVN' => 'Slovenia',
      'PRK' => 'North Korea',
      'KWT' => 'Kuwait',
      'SEN' => 'Senegal',
      'SMR' => 'San Marino',
      'SLE' => 'Sierra Leone',
      'SYC' => 'Seychelles',
      'KAZ' => 'Kazakhstan',
      'CYM' => 'Cayman Islands',
      'SGP' => 'Singapore',
      'SWE' => 'Sweden',
      'SDN' => 'Sudan',
      'DOM' => 'Dominican Republic',
      'DMA' => 'Dominica',
      'DJI' => 'Djibouti',
      'DNK' => 'Denmark',
      'VGB' => 'British Virgin Islands',
      'DEU' => 'Germany',
      'YEM' => 'Yemen',
      'DZA' => 'Algeria',
      'USA' => 'United States',
      'URY' => 'Uruguay',
      'MYT' => 'Mayotte',
      'UMI' => 'United States Minor Outlying Islands',
      'LBN' => 'Lebanon',
      'LCA' => 'Saint Lucia',
      'LAO' => 'Laos',
      'TUV' => 'Tuvalu',
      'TWN' => 'Taiwan',
      'TTO' => 'Trinidad and Tobago',
      'TUR' => 'Turkey',
      'LKA' => 'Sri Lanka',
      'LIE' => 'Liechtenstein',
      'LVA' => 'Latvia',
      'TON' => 'Tonga',
      'LTU' => 'Lithuania',
      'LUX' => 'Luxembourg',
      'LBR' => 'Liberia',
      'LSO' => 'Lesotho',
      'THA' => 'Thailand',
      'ATF' => 'French Southern Territories',
      'TGO' => 'Togo',
      'TCD' => 'Chad',
      'TCA' => 'Turks and Caicos Islands',
      'LBY' => 'Libya',
      'VAT' => 'Vatican',
      'VCT' => 'Saint Vincent and the Grenadines',
      'ARE' => 'United Arab Emirates',
      'AND' => 'Andorra',
      'ATG' => 'Antigua and Barbuda',
      'AFG' => 'Afghanistan',
      'AIA' => 'Anguilla',
      'VIR' => 'U.S. Virgin Islands',
      'ISL' => 'Iceland',
      'IRN' => 'Iran',
      'ARM' => 'Armenia',
      'ALB' => 'Albania',
      'AGO' => 'Angola',
      'ATA' => 'Antarctica',
      'ASM' => 'American Samoa',
      'ARG' => 'Argentina',
      'AUS' => 'Australia',
      'AUT' => 'Austria',
      'ABW' => 'Aruba',
      'IND' => 'India',
      'ALA' => 'Aland Islands',
      'AZE' => 'Azerbaijan',
      'IRL' => 'Ireland',
      'IDN' => 'Indonesia',
      'UKR' => 'Ukraine',
      'QAT' => 'Qatar',
      'MOZ' => 'Mozambique',
    ];
  }

  public function continentNamesFromGermanToEnglish() {
    return [
      'Asien' => 'Asia',
      'Afrika' => 'Africa',
      'Antarktis' => 'Antarctica',
      'Nordamerika' => 'North America',
      'Europa' => 'Europe',
      'Südamerika' => 'South America',
      'Australien' => 'Australia',
      'Zentralamerika' => 'Central America',
    ];
  }
}
