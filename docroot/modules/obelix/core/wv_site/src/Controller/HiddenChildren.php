<?php

namespace Drupal\wv_site\Controller;

use Drupal\child\Controller\ChildController;
use Drupal\child\Entity\Child;
use Drupal\Core\Controller\ControllerBase;
use \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class HiddenChildren.
 *
 * @package Drupal\wv_site\Controller
 */
class HiddenChildren extends ControllerBase
{
  /**
   * Page for checking found child & sponsor by clicking child widget button.
   */
  public function content(string $child_alias)
  {
    if (!$child_alias) {
      throw new NotFoundHttpException();
    }

    // Temporary drop the page cache to avoid anonym session problems.
    \Drupal::service('page_cache_kill_switch')->trigger();

    $child_controller = new ChildController();

    /** @var Child $child */
    $child = $child_controller->getChildByAlias($child_alias);

    if ($child && $child->field_reserved->value) {
      // Set child for user only if child is not sponsored, so user isn't able to sponsor child if he/she
      // would access the form page directly.
      if (!$child_is_sponsored = !$child_controller->checkBlockedChildren($child, TRUE)) {
        $child_controller->setChildForUser($child, TRUE, TRUE);
      }

      $country = $child->getCountry();
      $country_name = $country->getName();

      $sponsorship_child_section = [
        '#theme' => 'editorial_child_sponsorship_child_section',
        '#id' => 1,
        '#hidden_child_page' => TRUE,
        '#hide_sponsor_button' => $child_is_sponsored,
        '#sponsor_form_url' => \Drupal::config('wv_site.settings.routing')->get('sponsor_child_form_url'),
        '#child_image' => $child_controller->getChildImage($child, "square_xs"),
        '#child_image_src' => $child_controller->getChildImageSrc($child),
        '#child_video_src' => $child->getChildVideoUrl()->value,
        '#child_info' => [
          'lives_with' => $child->get('field_child_liveswithdesc')->value,
          'age' => $child->getAge(),
          'birthday' => \Drupal::service('date.formatter')->format(strtotime(substr($child->getBirthdate()->value, 0, 10)), 'custom', 'd.m.Y'),
          'likes' => $child->get('field_child_playdesc')->value,
          'country' => $country_name,
          'special_country_article' => $country->getSpecialCountryArticle(),
          'name' => $child->getGivenName()->value,
        ],
        '#cache' => [
          'max-age' => 0,
        ],
        '#attributes' => [
          'class' => [
            'first',
          ],
        ],
      ];

      $project = $child->getproject();
      $config = \Drupal::config('wv_site.settings.children');

      $build[] = array(
        '#type' => 'inline_template',
        '#template' => trim($config->get('hidden_child_layout')),
        '#attached' => [
          'library' => [
            // If above widget would be without video without video js it will fail.
            'child_sponsorship/child-select-video',
            'wv_site/videojs-youtube',
            'editorial/copy',
          ],
        ],
        '#context' => [
          'sponsorship_child_section' => $sponsorship_child_section,
          'child_is_sponsored' => $child_is_sponsored,
          'left_region' => array(
            '#type' => 'inline_template',
            '#template' => trim($config->get('hidden_children_left_region')),
            '#context' => [
              'childtext1' => $child->field_child_childtext1->value,
              'childtext2' => $child->field_child_childtext2->value,
              'childtext3' => $child->field_child_childtext3->value,
              'project_name' => $project ? $project->getName() : '',
              'projecttext1' => $project ? $project->field_projecttext1->value : '',
              'child_firstname' => $child->getGivenName()->value,
              'hide_sponsor_button' => $child_is_sponsored,
              'sponsor_form_url' => \Drupal::config('wv_site.settings.routing')->get('sponsor_child_form_url'),
            ],
          ),
          'right_region' => array(
            '#type' => 'inline_template',
            '#template' => trim($config->get('hidden_children_right_region')),
            '#context' => [],
          ),
        ],
        '#cache' => [
          'max-age' => 0,
        ],
      );

    } else {
      throw new NotFoundHttpException();
    }

    return $build;
  }

}
