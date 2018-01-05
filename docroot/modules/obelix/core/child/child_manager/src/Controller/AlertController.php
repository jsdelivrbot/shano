<?php

namespace Drupal\child_manager\Controller;

use Drupal\child\Entity\Child;
use Drupal\project\Entity\Project;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;
use Drupal\Core\Database\Driver\mysql\Select;



/**
 * Class AlertManager.
 *
 * @package Drupal\alert_controller\Controller
 */
class AlertController extends ControllerBase
{

    /**
     * Less than 10 children per country
     *
     * @return array
     * Returns the countries with less than 10 children.
     */
    public function countryAlert()
    {

        $project_entity_type = $this->entityTypeManager()->getStorage('project');
        /** @var Project $project */
        $projects = $project_entity_type->loadMultiple();

        $country_base = [];

        foreach ($projects as $project) {
            $country_base[$project->getCountry()->getCountryCode()] = [
                'country_code' => $project->getCountry()->getCountryCode(),
                'country_name' => $project->getCountry()->getName(),
                'child_amount' => 0,
                'male' => 0,
                'female' => 0,
            ];
        }

        $child_entity_type = $this->entityTypeManager()->getStorage('child');

        $children = $child_entity_type->loadByProperties(['status' => 1]);

        foreach ($children as $child) {
            /** @var Child $child */
            $country_code = $child->getCountry()->getCountryCode();
            $country_base[$country_code]['child_amount'] += 1;
            $gender = $child->getGenderDesc();

            switch (child_gender_check($gender)) {
              case 'female':
                $country_base[$country_code]['female'] += 1;
                break;

              case 'male':
                $country_base[$country_code]['male'] += 1;
                break;
            }
        }

        foreach ($country_base as $key => $row) {
            $child_amount[$key] = $row['child_amount'];
            $country_name[$key] = $row['country_name'];
        }

        array_multisort($child_amount, SORT_ASC, $country_name, SORT_ASC, SORT_STRING, $country_base);

        $content  = 'Hier wird ein Array ($country_base) mit allen Ländern, fuer die es Projekte gibt aufgebaut. ';
        $content .= "Kinder werden pro Land gezaehlt und nach Geschlecht unterschieden.<hr>";
        $content .= 'Task ... Bitte auch wieder die Email Alerts einstellen (an online-marketing@worldvision.de) wenn <br>';
        $content .=  'kleiner gleich 10 Jungen oder Mädchen für ein Land verfügbar sind verfügbar sind';


        return [
            '#type' => 'markup',
            '#markup' => $content,
        ];
    }


    public function birthdayAlert($childamount)
    {

        $child_entity_type = $this->entityTypeManager()->getStorage('child');

        $day = '';
        $mm = '';
        $mmdd = '';
        $mname = '';
        $search = '';

        for ($birthday = strtotime("2016-01-01"); $birthday <= strtotime("2016-12-31"); $birthday = strtotime("+1 day", $birthday)) {
            $mmdd = date("m-d", $birthday);
            $dd = date("d", $birthday);
            $mm = date("m", $birthday);
            $mname = date("M", $birthday);
            $search = '%-' . $mmdd . '%';
            $children = count($this->searchChildBirthday($search));
            if ($children < $childamount) {
                $color = 'red';
            } else {
                $color = 'green';
            }
            $tp[] = array('month' => $mm,
                'day' => $dd,
                'childsperday' => $children,
                'color' => $color,
                'mname' => $mname,);
        }

        $content  = 'Hier wird ein Array mit ($tp) allen Tagen des Jahres und den verfuegbaren Kindern aufgebaut. ';
        $content .= "Der Parameter gibt die kritische Anzahl der Kinder an.<hr>";
        $content .= 'Task ... Bitte auch wieder die Email Alerts einstellen (an online-marketing@worldvision.de) wenn ';
        $content .=  'kleiner gleich 2 Kinder für einen bestimmten Geburtstag verfügbar sind';


        return [
            '#type' => 'markup',
            '#markup' => $content,
        ];

    }

    /**
     * Search Child birthday entry's with the $value.
     * @param $value
     *  The LIKE value.
     * @return array
     *  List of Children ID.
     */
    private function searchChildBirthday($value)
    {
        $db = Database::getConnection();
        /** @var Select $results */
        $results = $db->select('child__field_child_birthdate', 'birthdate')
            ->fields('birthdate', ['field_child_birthdate_value', 'entity_id'])
            ->condition('field_child_birthdate_value', $value, 'LIKE');
        $result = $results->execute()->fetchAllKeyed(1);

        return $result;
    }

}
