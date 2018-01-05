<?php

namespace Drupal\c171\Controller;

use Drupal\child\Controller\ChildController;
use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Controller\ControllerBase;

class LandingController extends ControllerBase {

  /**
   * This module extension object.
   *
   * @var \Drupal\Core\Extension\Extension
   */
  protected $module;

  /**
   * The child management controller.
   *
   * @var \Drupal\child\Controller\ChildController
   */
  protected $childController;

  /**
   * LandingController constructor.
   */
  public function __construct() {
    $this->childController = new ChildController();

    $this->module = $this->moduleHandler()
      ->getModule('c171');
  }

  /**
   * Page callback for path '/campaigns/2017/1/{slideshow}/{video}'.
   *
   * @param $slideshow
   *   A editorial content type, preferably a slideshow.
   * @param $video
   *   A editorial content type, preferably a video.
   *
   * @return array
   *   Page content as renderable array.
   */
  public function index($slideshow, $video) {
    $content = [];

    $content['slideshow'] = \Drupal::entityTypeManager()
      ->getViewBuilder('editorial_content')
      ->view($slideshow, 'full');

    $content['video'] = \Drupal::entityTypeManager()
      ->getViewBuilder('editorial_content')
      ->view($video, 'full');

    $content['images'] = $this->getResponsiveImages();

    $content['child_section'] = $this->buildChildSection();

    $content['social_shares'] = $this->buildSocialShares();

    return [
      '#theme' => 'c171_page',
      '#content' => $content,
      '#base_path' => $this->getBasePath(),
    ];
  }

  /**
   * Get all responsive images.
   *
   * @return array
   *   An array of renderable responsive image sets.
   */
  protected function getResponsiveImages() {
    $images = [];

    $responsive_base_path = $this->getBasePath() . '/assets/images/';

    $images['stage'] = $this->buildResponsiveImage([
      'pattern' => $responsive_base_path . 'stage_{{device}}.jpg',
      'alt' => 'Billy & Urs – Die Geschichte einer Freundschaft',
    ]);
    $images['billy'] = $this->buildResponsiveImage([
      'pattern' => $responsive_base_path . 'billy_{{device}}.jpg',
      'alt' => 'Patenkind Billy in Simbabwe',
    ]);
    $images['schule'] = $this->buildResponsiveImage([
      'pattern' => $responsive_base_path . 'schule_{{device}}.jpg',
      'alt' => 'Schule in Simbabwe',
    ]);
    $images['farm'] = $this->buildResponsiveImage([
      'pattern' => $responsive_base_path . 'farm_{{device}}.jpg',
      'alt' => 'Farm in Simbabwe',
    ]);

    return $images;
  }

  /**
   * Gets the Child Section.
   *
   * @return array
   *   A renderable array.
   */
  protected function buildChildSection() {
    /** @var \Drupal\child\Entity\Child $child */
    $child = $this->childController->getRandomChild([
      'country' => 'ZIM',
    ]);

    if ($child === NULL) {
      return [];
    }

    return [
      '#theme' => 'editorial_child_sponsorship_child_section',
      '#id' => 'child-sponsorship-finder',
      '#child_image' => $this->childController->getChildImage($child, "square_xs"),
      '#child_image_src' => $this->childController->getChildImageSrc($child),
      '#child_video_src' => $child->getChildVideoUrl()->value,
      '#child_info' => [
        'lives_with' => $child->get('field_child_liveswithdesc')->value,
        'age' => $child->getAge(),
        'birthday' => \Drupal::service('date.formatter')
          ->format(strtotime(substr($child->getBirthdate()->value, 0, 10)), 'custom', 'd.m.Y'),
        'likes' => $child->get('field_child_playdesc')->value,
        'country' => $child->getCountry()->getName(),
        'special_country_article' => $child->getCountry()
          ->getSpecialCountryArticle(),
        'name' => $child->getGivenName()->value,
      ],
      '#cache' => [
        'max-age' => 0,
      ],
      '#attributes' => [
        'class' => ['child-section'],
      ],
    ];
  }

  /**
   * Builds an renderable array for a responsive image set.
   *
   * @param array $image
   *  An associative array with the following key values:
   *   - $pattern: A filename pattern with a placeholder {{device}} which will be replaced with 'mobile', 'tablet' and 'desktop'.
   *   - $alt: A alternative text for the image.
   *   - $mime: The mime type of the image.
   * @return array
   */
  protected function buildResponsiveImage(array $image) {
    $image = NestedArray::mergeDeep([
      'pattern' => '',
      'alt' => '',
      'mime' => '',
    ], $image);

    return [
      '#theme' => 'c171_responsive_image',
      '#src_pattern' => $image['pattern'],
      '#alt' => $image['alt'],
      '#mime' => $image['mime']
    ];
  }

  protected function buildSocialShares() {
    return [
      '#theme' => 'editorial_social_share',
      '#content' => [
        'share_text' => 'Seite Teilen',
        'page_title' => 'Die Geschichte einer Freundschaft',
        'page_path' => \Drupal::request()->getScheme() . '://' .
          \Drupal::request()->getHttpHost() .
          \Drupal::request()->getBaseUrl() .
          \Drupal::request()->getRequestUri(),
      ]
    ];
  }

  /**
   * Gets the base path to this module.
   *
   * @return string
   */
  protected function getBasePath() {
    return base_path() .
      $this->module->getPath();
  }
}
