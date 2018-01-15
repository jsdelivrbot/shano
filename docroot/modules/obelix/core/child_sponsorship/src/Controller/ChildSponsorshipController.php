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
class ChildSponsorshipController extends ControllerBase
{
  /**
   * Childsponsorshipaction.
   *
   * @return string
   *   Return Hello string.
   */
  //


  public function ChildSponsorshipAction()
  {
//    Redirect user if he has already seen the ab-test version
    if(isset($_COOKIE['child_sponsorship_v_billy']) && $_COOKIE['child_sponsorship_v_billy'] == 1){
      $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]";

      header('Location: '.$url.'/kinderpatenschaft-billy-und-urs');
    }

    $build = [

    ];

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

//      TODO @Voegler make it beauty
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
      '#theme' => "child_sponsorship_section_child_sponsorship_experience",
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
          'special_country_article' => $child->getCountry()->getSpecialCountryArticle(),
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

    return $build;
  }

  /**
   * Callback for child direct link.
   *
   * @param $id
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   */
  public function SetChildById($id) {
    // Get all data for current child.
    $child_data = Child::load($id);
    // Check that we have object with needed data.
    if (is_object($child_data)) {
      // Set current child ID in session for default displaying.
      $child_controller = new ChildController();
      $child_controller->setChildForUser($child_data, TRUE);
    }
    return $this->redirect('child_sponsorship.child_sponsorship_controller_ChildSponsorshipAction');
  }
}
