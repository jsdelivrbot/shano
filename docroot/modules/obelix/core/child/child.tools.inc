<?php

/**
 * Child tool callbacks for non OOP usage.
 */

/**
 * Check whether gender is male or female.
 *
 * @param $value string the value to check e.g. 'weiblich'
 * @param $gender string can be one of these values ['male', 'female']
 *
 * @return bool|string
 */
function child_gender_check($value, $gender = NULL) {

  switch ($value) {
    case 'männlich':
    case 'boy':
    case 'male':
      if ($gender) {
        if ($gender == 'male') {
          return TRUE;
        }
      }
      else {
        return 'male';
      }
      break;

    case 'weiblich':
    case 'girl':
    case 'female':
      if ($gender) {
        if ($gender == 'female') {
          return TRUE;
        }
      }
      else {
        return 'female';
      }
      break;
  }

  return FALSE;
}
