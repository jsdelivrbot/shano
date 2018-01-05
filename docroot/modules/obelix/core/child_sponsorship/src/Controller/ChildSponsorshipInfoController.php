<?php

namespace Drupal\child_sponsorship\Controller;

use Drupal\child\Controller\ChildController;
use Drupal\Core\Controller\ControllerBase;
use Drupal\editorial_content\Entity\EditorialContent;
use Drupal\Core\Entity\EntityInterface;

/**
 * Class ChildSponsorshipInfoController.
 *
 * @package Drupal\child_sponsorship\Controller
 */
class ChildSponsorshipInfoController extends ControllerBase {
  /**
   * Childsponsorshipinfoaction.
   *
   * @return string
   *   Return Hello string.
   */


  public function ChildSponsorshipInfoAction() {
    $build = [

    ];

    $build[] = [
      '#theme' => "child_sponsorship_info_section_header",
      '#content' => [

      ],
      '#content_attributes' => [],
      '#attributes' => [],
    ];
    $build[] = [
      '#theme' => "child_sponsorship_info_section_navbar",
      '#content' => [

      ],
      '#content_attributes' => [],
      '#attributes' => [],
    ];
    $build[] = [
      '#theme' => "child_sponsorship_info_section_child_sponsorship",
      '#content' => [

      ],
      '#content_attributes' => [],
      '#attributes' => [],
    ];

    $build[] = [
      '#theme' => "child_sponsorship_section_child_finder_teaser",
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
      '#theme' => "child_sponsorship_info_section_help_impact",
      '#content' => [],
      '#content_attributes' => [],
      '#attributes' => [],
      '#id' => 1
    ];

//    Load automatic-teaser from editorial-content with the specific ID you see below
    $editorialId = 7616;
    if (\Drupal::entityTypeManager()->getStorage('editorial_content')->load($editorialId) != null) {
      $content = [];

      $editorial_content = \Drupal::entityTypeManager()
        ->getStorage('editorial_content')
        ->load($editorialId);
      $content['automatic_teasers'] = \Drupal::entityTypeManager()
        ->getViewBuilder('editorial_content')
        ->view($editorial_content);

      if ($content) {
        $build[] = [
          '#markup' => '<div class="editorial-page"><div class="editorial-section"><div class="container-fluid has--editorial-content--story-teaser"><div class="row"><div class="col-xs-12"><div class="col-xs-12">',
        ];
        $build[] = [
          '#theme' => 'editorial_story_teaser',
          '#attributes' => [],
          '#content' => $content,
        ];
        $build[] = [
          '#markup' => '</div></div></div></div></div></div></div>',
        ];
      }
    }

    $build[] = [
      '#theme' => "child_sponsorship_info_section_sponsorship_experience",
      '#content' => [],
      '#content_attributes' => [],
      '#attributes' => [],
    ];
    $build[] = [
      '#theme' => "child_sponsorship_info_section_transparency",
      '#content' => [],
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
        '#markup' => '<div class="editorial-page"><div class="editorial-section"><div class="container-fluid no-padding-bottom first"><div class="row">',
      ];

      $build[] = [
        '#theme' => 'editorial_child_sponsorship_child_section',
        '#child_image' => $image = $child_controller->getChildImage($child, "square_xs"),
        '#child_info' => [
          'lives_with' => $child->get('field_child_liveswithdesc')->value,
          'age' => $child->getAge(),
          'birthday' => \Drupal::service('date.formatter')
            ->format(strtotime(substr($child->getBirthdate()->value, 0, 10)), 'custom', 'd.m.Y'),
          'likes' => $child->get('field_child_playdesc')->value,
          'country' => $child->getCountry()->getName(),
          'special_country_article' => $child->getCountry()->getSpecialCountryArticle(),
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

//      TODO @Voegler make it beauty
      $build[] = [
        '#markup' => '</div></div></div></div>',
      ];
    }



    return $build;
  }

}
