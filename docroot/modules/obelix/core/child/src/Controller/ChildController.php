<?php

namespace Drupal\child\Controller;

use Drupal\child\ChildInterface;
use Drupal\child\Entity\Child;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;
use Drupal\Core\Database\Driver\mysql\Select;
use Drupal\Core\Render\Markup;
use Drupal\file\Entity\File;

/**
 * Class ChildController.
 *
 * @package Drupal\child\Controller
 */
class ChildController extends ControllerBase {

  /**
   * @var
   * Stores the currently loaded child data.
   */
  protected static $childData;

   /**
   * Viewchild.
   *
   * @return string
   *   Return Hello string.
   */
  public function viewChild($ivision_id) {

    $child_entity_type = $this->entityTypeManager()->getStorage('child');
    $child_entity = $child_entity_type->load($ivision_id);

    // render the child image
    $file = File::load($child_entity->get('field_child_image')
      ->getValue()[0]['target_id']);

    $variables = array(
      'style_name' => 'child',
      'uri' => $file->getFileUri(),
    );

    // The image.factory service will check if image is valid.
    $image = \Drupal::service('image.factory')->get($file->getFileUri());

    $facedetection_image = [
      '#theme' => 'image_style',
      '#width' => $image->getWidth(),
      '#height' => $image->getHeight(),
      '#style_name' => 'child',
      '#uri' => $file->getFileUri(),
    ];

    $original_image = [
      '#theme' => 'image_style',
      '#width' => $image->getWidth(),
      '#height' => $image->getHeight(),
      '#style_name' => 'large',
      '#uri' => $file->getFileUri(),
    ];

    $content = [
      'original' => \Drupal::service('renderer')->render($original_image),
      'facedetection' => \Drupal::service('renderer')
        ->render($facedetection_image),
    ];

    return [
      '#theme' => 'child',
      '#content' => $content,
    ];
  }

  /**
   * Get the current child data from custom session and return it.
   *
   * @return mixed
   */
  private function loadChildData() {
    \Drupal::service('page_cache_kill_switch')->trigger();
    $session_manager = \Drupal::service('child.session_manager');
    return $session_manager->getData('children');
  }

  /**
   * Store the child data in custom session.
   *
   * @param $data
   *   The data to store.
   */
  private function saveChildData($data) {
    \Drupal::service('page_cache_kill_switch')->trigger();
    $session_manager = \Drupal::service('child.session_manager');
    $session_manager->setData('children', $data);
  }

  /**
   * Returns the Child from the user session.
   * If the session is empty it search for a new Child.
   *
   * @param bool $force_new
   *  Force random Child if the Child is blocked.
   * @return Child Returns the users Child.
   * Returns the users Child.
   */
  public function getChildFromUser($force_new = FALSE, $is_hidden_child = FALSE) {
    // Avoid fatal error for CLI process due to the  Drupal\user\PrivateTempStore->getOwner() null error.
    if (PHP_SAPI != 'cli') {
      // User have already seen a child.
      $children = $this->loadChildData();
      $child_entity_type = $this->entityTypeManager()->getStorage('child');
      // Now we have 2 storages for hidden & ordinary children.
      switch ($is_hidden_child) {
        case TRUE:
          if (isset($children['last_hidden_child'])) {
            /** @var Child $child */
            $child = $child_entity_type->load($children['last_hidden_child']);
          }
          break;
        default:
          if (isset($children['last_child'])) {
            $last_child_id = $children['last_child'];
            /** @var Child $child */
            $child = $child_entity_type->load($last_child_id);
          }
          else {
            // user have not seen a child on the page.
            if ($random_child = $this->getRandomChild()) {
              // Avoid showing the child on widget if user couldn't do lock as it won't be possible to sponsor &
              // will show different child on sponsor form which would cause confusion.
              if ($this->setChildForUser($random_child)) {
                return $random_child;
              }
            }
          }
          break;
      }
      if (!empty($child)) {
        if ($this->checkBlockedChildren($child, $is_hidden_child)) {
          // Child not blocked.
          return $child;
        }
        elseif (!$is_hidden_child) {
          // We don't need to keep child in user's session if the child is not available for user anymore.
          // E.g. was blocked by another user or was sponsored.
          if ($this->removeChildForUser($child) && !$force_new) {
            // Try to get child from user's session if it's not empty & last child wasn't verified.
            $children = $this->loadChildData();
            if (!empty($children['all_children'])) {
              // Recursion, once all not available children are deleted & there is no active child it will stop.
              return $this->getChildFromUser();
            }
          }
        }
      }
      if ($force_new) {
        // Child blocked but requested new Child.
        $random_child = $this->getRandomChild();
        // If child wasn't set for user by some reason we shouldn't return it back as
        // anyway this child won't be donated by sponsor coz child wasn't saved to session.
        if (!$this->setChildForUser($random_child)) {
          return NULL;
        }
        return $random_child;
      }
    }
    return NULL;
  }

  /**
   * Returns the Child by child alias.
   *
   * @param $alias string
   *
   * @return Child|null
   */
  public function getChildByAlias($alias) {
    if (!$alias || !is_string($alias)) {
      return NULL;
    }

    $child_entity_type = $this->entityTypeManager()->getStorage('child');

    /** @var Child $child */
    $children = $child_entity_type->loadByProperties(['field_child_alias' => $alias]);

    return $children ? reset($children) : NULL;
  }

  /**
   * Set a Child for the User in the Session.
   *
   * @param Child $child
   * @param bool $force
   *  Bypasses limit.
   * @param bool $is_hidden_child
   *  Used to avoid conflict when user access hidden child page, then go to another form set new child
   * and back to hidden sponsor form, it will update the selected child, so we store hidden children
   * separately.
   *
   * @return bool
   */
  public function setChildForUser($child, $force = FALSE, $is_hidden_child = FALSE) {
    $children = $this->loadChildData();

    if (!$children) {
      $children = [];
    }

    if ($is_hidden_child) {
      // Child was already watched by the user.
      if (isset($children['last_hidden_child']) && ($child->getIVisionID() == $children['last_hidden_child'])) {
        return TRUE;
      }
      $children['last_hidden_child'] = $child->getIVisionID();
      $this->saveChildData($children);
    }
    else {
      // Child was already watched by the user.
      if (isset($children['all_children']) && array_search($child->getIVisionID(), $children['all_children']) !== FALSE) {
        $children['last_child'] = $child->getIVisionID();
        $this->saveChildData($children);
        return TRUE;
      }
      $limit = \Drupal::config('wv_site.settings')->get('children_user_limit');
      if (!isset($limit)) {
        $limit = 4;
      }
      // User have already seen 4 children.
      if (!$force && isset($children['all_children']) && $limit && count($children['all_children']) >= $limit) {
        return FALSE;
      }
      $children['all_children'][] = $child->getIVisionID();
      $children['last_child'] = $child->getIVisionID();
      $this->saveChildData($children);
    }

    return TRUE;
  }

  /**
   * Remove a Child for the User in the Session.
   *
   * @param ChildInterface $child
   * If is child is null the last_child will be used.
   * @return bool
   */
  public function removeChildForUser(ChildInterface $child = NULL) {
    $children = $this->loadChildData();
    // check if user has children.
    if (empty($children)) {
      return FALSE;
    }

    // if child is null set last_child as child entity.
    if (!$child) {
      if (isset($children['last_child'])) {
        $child_entity_type = $this->entityTypeManager()->getStorage('child');
        /** @var ChildInterface $child */
        if (!$child = $child_entity_type->load($children['last_child'])) {
          return FALSE;
        }
      }
      else {
        return FALSE;
      }
    }

    // remove child from all_children
    if (isset($children['all_children'])) {
      foreach ($children['all_children'] as $key => &$iVisionID) {
        if ($iVisionID == $child->getIVisionID()) {
          unset($children['all_children'][$key]);
        }
      }
    }
    else {
      return FALSE;
    }

    // remove last_child if equal to child entity
    if ($children['last_child'] == $child->getIVisionID()) {
      unset($children['last_child']);
      // if user have another child in his list set an random child as last seen.
      if (!empty($children['all_children'])) {
        $rand_key = array_rand($children['all_children']);
        $children['last_child'] = $children['all_children'][$rand_key];
      }
    }
    $this->saveChildData($children);
    $this->unsetBlockedChildForUser($child);
    return TRUE;
  }


  /**
   * Returns all children user have watched.
   *
   * @return array|null
   */
  private function getUsersChildren() {
    $children = $this->loadChildData();
    if (isset($children['all_children'])) {
      return $children['all_children'];
    }
  }

  /**
   * Set a child in the user blocked children session.
   *
   * @param Child $child
   */
  public function setBlockedChildForUser($child) {
    $children = $this->loadChildData();
    if (!$this->checkBlockedChildren($child)) {
      $children['blocked_children'][] = $child->getIVisionID();
    }
    $this->saveChildData($children);
  }

  /**
   * Unblock a Child from User and delete it from his session.
   *
   * @param ChildInterface $child
   */
  public function unsetBlockedChildForUser(ChildInterface $child) {
    $children = $this->loadChildData();
    foreach ($children['blocked_children'] as $key => &$child_id) {
      if ($child->getIVisionID() == $child_id) {
        unset($children['blocked_children'][$key]);
      }
    }
    $this->saveChildData($children);
  }

  /**
   * Checks if the user is allowed to see the children.
   * If the children is blocked the users blocked children list will be checked.
   *
   * @param Child $child
   * @param bool $allow_hidden_child
   *  Hidden children aren't shown by ordinary pages,but you can see them on special pages.
   *
   * @return bool
   */
  public function checkBlockedChildren($child, $allow_hidden_child = FALSE) {
    // Check whether child can be shown in form widget.
    $status = $child->getStatus();
    switch (TRUE) {
      case $status == 0:
        // child sponsored
        return FALSE;
      // Reserved child can't be shown on non hidden child pages.
      case $status == 1 && !$child->field_reserved->value:
        // child not blocked
        return TRUE;
      case $status == 1 && $allow_hidden_child && $child->field_reserved->value:
        // child not blocked
        return TRUE;
      // Avoid showing hidden children if by some reason they were blocked (it's pretty unlikely case though).
      case $status == 2 && (!$child->field_reserved->value || ($allow_hidden_child && $child->field_reserved->value)):
        $session_children = $this->loadChildData();
        foreach ($session_children['blocked_children'] as $session_child) {
          if ($session_child == $child->getIVisionID()) {
            // user is allowed to see blocked children
            return TRUE;
          }
        }
        return FALSE;
      default:
        return FALSE;
    }
  }

  /**
   * Returns a new random child depending on the parameters.
   *
   * @param $args
   *  Could have to following keys.
   *  - gender: F, M
   *  - country: Country key
   *  - birthday: 01 - 31
   *  - birthmonth: 01 -12
   * @return Child
   *  Returns a Child depending on the arguments.
   */
  public function getRandomChild(array $args = []) {
    // Only get free children.
    $parameters['status'] = 1;

    $child_storage = $this->entityTypeManager()->getStorage('child');

    // Don't include reserved children in search as they can be accessed only by special page.
    if (wv_entity_type_has_field('child', 'field_reserved')) {
      // Avoid searching hidden children.
      $parameters['field_reserved'] = 0;
    }

    // If birthday nad birthmonth is given they have to concat in the correct way to search for the birthday.
    if (isset($args['birthday']) && isset($args['birthmonth'])) {
      $birthmonth = str_pad($args['birthmonth'], 2, '0', STR_PAD_LEFT);
      $birthday = str_pad($args['birthday'], 2, '0', STR_PAD_LEFT);
      $args['birthdate'] = $birthmonth . '-' . $birthday;

      unset($args['birthday'], $args['birthmonth']);
    }

    // Check if user have already watched a child with the same search country.
    if (isset($args['country'])) {

      // Avoid using this in CLI as it throws fatal.
      if (PHP_SAPI !== 'cli' && !empty($user_children = $this->getUsersChildren())) {
        foreach ($child_storage->loadMultiple($user_children) as $child) {
          $country = $child ? $child->getCountry() : NULL;

          /** @var Child $child */
          if ($country && $country->getCountryCode() == $args['country']) {
            if ($this->checkBlockedChildren($child)) {
              if (!\Drupal::config('wv_site.settings')->get('allow_different_children_from_the_same_country')) {
                return $child;
              }
            }
            else {
              // We don't need to keep child in user's session if the child is not available for user anymore.
              // E.g. was blocked by another user or was sponsored.
              $this->removeChildForUser($child);
            }
          }
        }
      }
    }

    foreach ($args as $arg_key => $arg_value) {
      switch ($arg_key) {
        case 'gender':
          $parameters['field_child_genderdesc'] = $arg_value;
          break;

        case 'country':
          if (is_null($arg_value)) {
            return NULL;
          }

          // get all projects for country.
          $child_entity_type = $this->entityTypeManager()->getStorage('project');

          // If country is "0" we want to query all countries.
          if (is_numeric($arg_value) && $arg_value == 0) {
            $projects = $child_entity_type->loadMultiple();
          }
          else {
            $projects = $child_entity_type->loadByProperties(['field_country' => $arg_value]);
          }

          if (empty($projects)) {
            return NULL;
          }

          foreach ($projects as $project_id => $project) {
            $parameters['field_child_project'][] = $project_id;
          }
          break;

        case 'birthday':
          $children = $this->searchChildBirthday('%-' . str_pad($arg_value,2, '0', STR_PAD_LEFT));
          if (empty($children)) {
            return NULL;
          }

          $parameters['ivision_id'] = reset($children);
          break;

        case 'birthmonth':
          $children = $this->searchChildBirthday('%-' . str_pad($arg_value,2, '0', STR_PAD_LEFT) . '-%');
          if (empty($children)) {
            return NULL;
          }

          $parameters['ivision_id'] = reset($children);
          break;

        case 'birthdate':
          $children = $this->searchChildBirthday('%-' . $arg_value);
          if (empty($children)) {
            return NULL;
          }

          $parameters['ivision_id'] = reset($children);
          break;
      }
    }

    $child_entity_type = $this->entityTypeManager()->getStorage('child');
    $children = $child_entity_type->loadByProperties($parameters);

    if (empty($children)) {
      return NULL;
    }

    $rand_key = array_rand($children);

    // Set config var skip default countries, defaults to false if false use default
    // countries (do not break DE) if true skip this.
    if ($skip = \Drupal::config('wv_site.settings')->get('skip_default_countries')) {
      return $children[$rand_key];
    }

    // Shuffle children and find a children from default countries.
    $default_countries = ['ZIM', 'SIE', 'PER', 'IDN', 'TCD'];

    foreach ($children as $child_id => $child) {
      /** @var Child $child */
      if ($child->getCountry() && in_array($child->getCountry()->getCountryCode(), $default_countries)) {
        $rand_key = $child_id;
        break;
      }
    }

    return $children[$rand_key];
  }

  /**
   * Search Child birthday entry's with the $value.
   * @param $value
   *  The LIKE value.
   * @return array
   *  List of Children ID.
   */
  private function searchChildBirthday($value) {
    $db = Database::getConnection();
    /** @var Select $results */
    $results = $db->select('child__field_child_birthdate', 'birthdate')
      ->fields('birthdate', ['field_child_birthdate_value', 'entity_id'])
      ->condition('birthdate.field_child_birthdate_value', $value, 'LIKE');

    $results->join('child', 'c', 'c.ivision_id = birthdate.entity_id AND status = 1');
    $result = $results->execute()->fetchAllKeyed(1);

    return $result;
  }


  /**
   * Returns the child image markup depending on the given image_style.
   *
   * @param Child $child
   * @param string $image_style
   * @return Markup
   */
  public static function getChildImage(Child $child, $image_style = 'large') {

    // load the child image
    $file = File::load($child->get('field_child_image')
      ->getValue()[0]['target_id']);

    $uri = $file ? $file->getFileUri() : NULL;

    // The image.factory service will check if image is valid.
    $image = \Drupal::service('image.factory')->get($uri);

    $image_theme = [
      '#theme' => 'image_style',
      '#width' => $image->getWidth(),
      '#height' => $image->getHeight(),
      '#style_name' => $image_style,
      '#uri' => $uri,
    ];

    $image_container = [
      '#theme' => 'child_image',
      '#child' => $image_theme,
      '#cache' => [
        'max-age' => 0,
      ],
    ];

    return \Drupal::service('renderer')->render($image_container);


  }

  /**
   * Returns the child image src
   *
   * @param Child $child
   * @return String
   */
  public static function getChildImageSrc(Child $child) {
    // load the child image
    $file = File::load($child->get('field_child_image')
      ->getValue()[0]['target_id']);

    // get child image src
    $image_src = $file->url();

    return $image_src;

  }


  /**
   * Returns the gender list for Child as assoc array.
   *
   * @return array
   *  child gender assoc array.
   */
  public static function getGenderOptions() {
    $db = Database::getConnection();
    $query = $db->select('child__field_child_genderdesc', 'gender');
    $query->join('child', 'c', 'c.ivision_id = gender.entity_id AND status = 1');
    $query->fields('gender', ['field_child_genderdesc_value'])->distinct();

    $options = $query->execute()->fetchAllKeyed(0, 0);

    $original_options = $options;

    // @todo please set up a mapping function for all iVision fields
    $options['weiblich'] = 'girl';
    $options['männlich'] = 'boy';

    $moduleHandler = \Drupal::service('module_handler');

    // Let other modules alter cart.
    $moduleHandler->alter('child_gender_options', $options, $original_options);

    if (forms_suite_is_child_sponsorship_english_form()) {
      foreach ($options as &$value) {
        $value = ucfirst($value);
      }
    }
    // Translate all gender options.
    array_walk($options, function (&$gender) {
      $gender = t($gender);
    });

    return $options;
  }


  /**
   * Checks if email is already used for blocking child.
   *
   * @param $email
   * @return bool
   */
  public static function checkBlockedEmail($email) {
    $child_entity_type = \Drupal::entityTypeManager()->getStorage('child');
    $children = $child_entity_type->loadByProperties([
      'blocked_from' => $email
    ]);
    if (count($children) > 0) {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }
}
