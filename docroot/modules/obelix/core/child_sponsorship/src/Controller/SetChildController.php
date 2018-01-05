<?php

namespace Drupal\child_sponsorship\Controller;

use Drupal\child\Controller\ChildController;
use Drupal\child\Entity\Child;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class SetChildController.
 *
 * @package Drupal\child_sponsorship\Controller
 */
class SetChildController extends ControllerBase {


  /**
   * Set Child for user in session and
   * redirect to child sponsorship form.
   *
   * @param $uuid
   * @return RedirectResponse
   */
  public function setChild($uuid) {

    $child_entity_type = \Drupal::entityTypeManager()->getStorage('child');
    $children = $child_entity_type->loadByProperties([
      'uuid' => $uuid
    ]);
    foreach ($children as $child){
      $child_controller = new ChildController();
      $child_controller->setChildForUser($child);
      $child_controller->setBlockedChildForUser($child);
    }

    $url = Url::fromUserInput('/forms/child_sponsorship_with_condition');
    return new RedirectResponse($url->toString());
  }

}


