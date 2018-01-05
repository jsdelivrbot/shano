<?php

namespace Drupal\child\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\child\Entity\Child;
use Drupal\project\Entity\Project;
use Drupal\Core\Database\Database;
use Drupal\Core\Database\Driver\mysql\Select;


/**
 * Class ContryController.
 *
 * @package Drupal\child\Controller
 */
class StatisticsController extends ControllerBase
{
    public function countryPage()
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


        return [
            '#theme' => 'child_country',
            '#content' => $country_base,
        ];

    }


    public function birthdayPage($childamount){

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
            $search = '%-'.$mmdd.'%';
            $children = count($this->searchChildBirthday($search));
            if($children < $childamount){
                $color = 'red';
            } else {
                $color = 'green';
            }
            $tp[] = array('month' => $mm ,
                'day'   => $dd,
                'childsperday' => $children,
                'color' => $color,
                'mname' => $mname,);
        }

        return [
            '#theme' => 'child_birthday',
            '#tps' => $tp,
            '#childamount' => $childamount,
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
