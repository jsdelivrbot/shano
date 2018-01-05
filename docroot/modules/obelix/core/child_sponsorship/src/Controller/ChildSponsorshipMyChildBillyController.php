<?php

namespace Drupal\child_sponsorship\Controller;

use Drupal\child\Controller\ChildController;
use Drupal\child\Entity\Child;
use Drupal\Core\Controller\ControllerBase;
use Drupal\editorial_content\Entity\EditorialContent;

/**
 * Class ChildSponsorshipController.
 *
 * @package Drupal\child_sponsorship\Controller
 */
class ChildSponsorshipMyChildBillyController extends ControllerBase
{
  /**
   * ChildsponsorshipMyChildBillyaction.
   *
   * @return string
   *   Return Hello string.
   */
  //


  public function ChildSponsorshipMyChildBillyAction()
  {
    $build = [

    ];

    $build[] = [
      '#theme' => "child_sponsorship_my_child_billy_section_header",
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

//      TODO @Voegler make it beauty
      $build[] = [
        '#markup' => '<div class="editorial-page"><div class="editorial-section"><div class="container-fluid first"><div class="row">',
      ];

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
//        $build[2]['#cache']['keys'][] = ($child->getIVisionID() + time());
//        $build[2]['#cache']['max-age'] = 0;
//        \Drupal::service('page_cache_kill_switch')->trigger();
//        var_dump($build[2]);
//      TODO @Voegler make it beauty
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

    $build[] = [
      '#theme' => "child_sponsorship_section_faq",
      '#content' => [

      ],
      '#content_attributes' => [],
      '#attributes' => [],
    ];
    $build[] = [
      '#theme' => "child_sponsorship_section_help",
      '#content' => [

      ],
      '#content_attributes' => [],
      '#attributes' => [],
    ];
    $build[] = [
      '#theme' => "child_sponsorship_section_my_worldvision_preview",
      '#content' => [

      ],
      '#content_attributes' => [],
      '#attributes' => [],
    ];
    $build[] = [
      '#theme' => "child_sponsorship_my_child_billy_section_child_sponsorship_experience",
      '#content' => [

      ],
      '#content_attributes' => [],
      '#attributes' => [],
    ];
    if ($child) {

      \Drupal::service('page_cache_kill_switch')->trigger();

//      TODO @Voegler make it beauty
      $build[] = [
        '#markup' => '<div class="editorial-page"><div class="editorial-section"><div class="container-fluid no-padding-bottom first"><div class="row">',
      ];

      $build[] = [
        '#theme' => 'editorial_child_sponsorship_child_section',
        '#id' => 2,
        '#child_image' => $image = $child_controller->getChildImage($child, "square_xs"),
        '#child_image_src' => $child_controller->getChildImageSrc($child),
        '#child_video_src' => $child->getChildVideoUrl()->value,
        '#child_info' => [
          'lives_with' => $child->get('field_child_liveswithdesc')->value,
          'age' => $child->getAge(),
          'birthday' => \Drupal::service('date.formatter')->format(strtotime(substr($child->getBirthdate()->value, 0, 10)), 'custom', 'd.m.Y'),
          'likes' => $child->get('field_child_playdesc')->value,
          'country' => $child->getCountry()->getName(),
          'name' => $child->getGivenName()->value,
        ],
        '#cache' => [
          'max-age' => 0,
        ],
        '#attributes' => [
          'class' => [
            'no-padding-bottom',
          ],
        ],
      ];

//        $build[2]['#cache']['keys'][] = ($child->getIVisionID() + time());
//        $build[2]['#cache']['max-age'] = 0;
//        \Drupal::service('page_cache_kill_switch')->trigger();
//        var_dump($build[2]);
//      TODO @Voegler make it beauty
      $build[] = [
        '#markup' => '</div></div></div></div>',
      ];
    }
    $build[] = [
      '#theme' => "child_sponsorship_section_child_sponsorship_suggestion",
      '#content' => [

      ],
      '#content_attributes' => [],
      '#attributes' => [],
    ];

//    Cookie is prompted in ChildSponsorshipController
    setcookie ("child_sponsorship_v_billy", "1", 0);

    return $build;
  }

}


