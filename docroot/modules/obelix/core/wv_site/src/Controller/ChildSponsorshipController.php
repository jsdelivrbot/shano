<?php

namespace Drupal\wv_site\Controller;

use Drupal\child\Controller\ChildController;
use Drupal\child\Entity\Child;
use Drupal\Core\Controller\ControllerBase;
use Drupal\editorial_content\Entity\EditorialContent;

/**
 * Class ChildSponsorshipController.
 *
 * @package Drupal\wv_site\Controller
 */
class ChildSponsorshipController extends ControllerBase
{
  /**
   * Page for checking found child & sponsor by clicking child widget button.
   */
  public function ChildSponsorshipAction()
  {
    $build = [];

    $build[] = [
      '#theme' => "child_sponsorship_section_header",
      '#content' => [

      ],
      '#content_attributes' => [],
      '#attributes' => [],
    ];

    $child_controller = new ChildController();
    /** @var Child $child */
    $child = $child_controller->getChildFromUser(TRUE);

    if ($child) {

      \Drupal::service('page_cache_kill_switch')->trigger();

      $build[] = [
        '#markup' => '<div class="editorial-page"><div class="editorial-section"><div class="container-fluid first"><div class="row">',
      ];

      $country = $child->getCountry();

      $build[] = [
        '#theme' => 'editorial_child_sponsorship_child_section',
        '#id' => 1,
        '#child_image' => $child_controller->getChildImage($child, "square_xs"),
        '#child_image_src' => $child_controller->getChildImageSrc($child),
        '#child_video_src' => $child->getChildVideoUrl()->value,
        '#child_info' => [
          'lives_with' => $child->get('field_child_liveswithdesc')->value,
          'age' => $child->getAge(),
          'birthday' => \Drupal::service('date.formatter')->format(strtotime(substr($child->getBirthdate()->value, 0, 10)), 'custom', 'd.m.Y'),
          'likes' => $child->get('field_child_playdesc')->value,
          'country' => $child->getCountry()->getName(),
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

      $build[] = [
        '#markup' => '</div></div></div></div>',
      ];
    }

    $build[] = [
      '#theme' => "child_sponsorship_section_child_finder",
      '#content' => [
        'form_child_finder_birthday' =>
          \Drupal::formBuilder()
            ->getForm('Drupal\child_sponsorship\Form\ChildFinderBirthday'),
        'form_child_finder_country' =>
          \Drupal::formBuilder()
            ->getForm('Drupal\child_sponsorship\Form\ChildFinderCountry'),
      ],
    ];

    return $build;
  }

}
