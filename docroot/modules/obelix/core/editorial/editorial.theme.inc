<?php

use Drupal\Component\Utility\Html;
use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Template\Attribute;
use Drupal\Core\Url;

/**
 * Prepares variables for Editorial Button templates.
 *
 * Default template: editorial-button.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - uri: The target uri.
 *   - title: The link text.
 *   - attributes: HTML attributes for the container element.
 *   - content_attributes: HTML attributes for the anchor element.
 *   - settings: An array with settings.
 */
function template_preprocess_editorial_button(&$variables) {
  $id = Html::getUniqueId('editorial-button--' . $variables['id']);

  $variables['settings'] = NestedArray::mergeDeep([
    'align' => 'left',
    'target' => '_self',
  ], $variables['settings']);


  if (!$variables['uri'] instanceof Url) {
    try {
      $variables['uri'] = Url::fromUri($variables['uri']);
    } catch(\Exception $e) {

    }
  }

  if (!$variables['uri'] instanceof Url) {
    try {
      $variables['uri'] = Url::fromUserInput($variables['uri']);
    } catch(\Exception $e) {

    }
  }

  $variables['content_attributes']['href'] = $variables['uri'];

  if ($variables['uri'] instanceof Url) {
    $variables['content_attributes']['href'] = $variables['content_attributes']['href']->toString();
  }

  $variables['content_attributes']['target'] = $variables['settings']['target'];

  $variables['attributes']['id'] = $id;

  switch ($variables['settings']['align']) {
    case 'right':
      $variables['attributes']['class'][] = 'text-right';
      break;
    case 'center':
      $variables['attributes']['class'][] = 'text-center';
      break;
  }

  $variables['#attached']['library'][] = 'editorial/button';
}


/**
 * Prepares variables for Editorial Link templates.
 *
 * Default template: editorial-link.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - content: The Link string.
 *   - attributes: HTML attributes for the container element.
 *   - content_attributes: HTML attributes for the anchor element.
 *   - settings: An array with settings.
 */
function template_preprocess_editorial_link(&$variables) {
  $id = Html::getUniqueId('editorial-link--' . $variables['id']);

  if (!$variables['uri'] instanceof Url) {
    try {
      $variables['uri'] = Url::fromUri($variables['uri']);
    } catch(\Exception $e) {

    }
  }

  if (!$variables['uri'] instanceof Url) {
    try {
      $variables['uri'] = Url::fromUserInput($variables['uri']);
    } catch(\Exception $e) {

    }
  }

  $variables['content_attributes']['href'] = $variables['uri'];

  if ($variables['uri'] instanceof Url) {
    $variables['content_attributes']['href'] = $variables['content_attributes']['href']->toString();
  }

  $variables['attributes']['id'] = $id;
  $variables['#attached']['library'][] = 'editorial/link';

}

/**
 * Prepares variables for Editorial Campaign Teaser templates.
 *
 * Default template: editorial-campaign-teaser.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - content: An array with the teaser content.
 *   - attributes: HTML attributes for the container element.
 *   - content_attributes: HTML attributes for the anchor element.
 *   - settings: An array with settings.
 */
function template_preprocess_editorial_campaign_teaser(&$variables) {
  $variables['settings'] = NestedArray::mergeDeep([
    'align' => 'left',
  ], $variables['settings']);

  if ($variables['settings']['align'] == 'right') {
    $variables['content_attributes']['class'][] = 'align-right';
  }

  $id = Html::getUniqueId('editorial-campaign-teaser--' . $variables['id']);
  $variables['attributes']['id'] = $id;
  $variables['#attached']['library'][] = 'editorial/campaign-teaser';
}

/**
 * Prepares variables for Editorial Copy templates.
 *
 * Default template: editorial-copy.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - content: An array with the teaser content.
 *   - attributes: HTML attributes for the container element.
 *   - content_attributes: HTML attributes for the anchor element.
 *   - settings: An array with settings.
 */
function template_preprocess_editorial_copy(&$variables) {
  $variables['settings'] = NestedArray::mergeDeep([
  ], $variables['settings']);

  $id = Html::getUniqueId('editorial-copy--' . $variables['id']);

  $variables['attributes']['id'] = $id;
  $variables['#attached']['library'][] = 'editorial/copy';
}

/**
 * Prepares variables for Editorial Headline templates.
 *
 * Default template: editorial-hero_slider.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - content: The headline string.
 *   - attributes: HTML attributes for the container element.
 *   - content_attributes: HTML attributes for the anchor element.
 *   - settings: An array with settings.
 */
function template_preprocess_editorial_headline(&$variables) {
  $id = Html::getUniqueId('editorial-headline--' . $variables['id']);

  $variables['settings'] = NestedArray::mergeDeep([
    'align' => 'left',
  ], $variables['settings']);

  switch ($variables['settings']['align']) {
    case 'right':
      $variables['attributes']['class'][] = 'text-right';
      break;
    case 'center':
      $variables['attributes']['class'][] = 'text-center';
      break;
  }

  $variables['attributes']['id'] = $id;
  $variables['#attached']['library'][] = 'editorial/headline';
}

/**
 * Prepares variables for Editorial Hero Slider templates.
 *
 * Default template: editorial-hero_slider.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - slides: An array of Hero Slider Slides.
 *   - attributes: HTML attributes for the container element.
 *   - content_attributes: HTML attributes for the anchor element.
 *   - settings: An array with settings.
 */
function template_preprocess_editorial_hero_slider(&$variables) {
  $id = Html::getUniqueId('editorial-hero-slider--' . $variables['id']);

  $variables['settings'] = NestedArray::mergeDeep([
    'dots' => true,
    'arrows' => false,
    'mobileFirst' => true,
    'adaptiveHeight' => true,
    'slidesToShow' => 1,
    'autoplay' => true, // TODO Live: TRUE
    'autoplaySpeed' => 6000,
    'speed' => 700,
    'infinite' => true,
    'cssEase' => 'ease-out',
    'responsive' => [],
  ], $variables['settings']);

  $variables['attributes']['id'] = $id;
  $variables['#attached']['library'][] = 'editorial/hero-slider';
  $variables['#attached']['drupalSettings']['editorial']['hero_slider'][$id] = $variables['settings'];
}

/**
 * Prepares variables for Editorial Hero Slider Slide templates.
 *
 * Default template: editorial-hero_slider_slide.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - content: An array with the teaser content.
 *   - attributes: HTML attributes for the container element.
 *   - content_attributes: HTML attributes for the anchor element.
 *   - settings: An array with settings.
 */
function template_preprocess_editorial_hero_slider_slide(&$variables) {
  $variables['settings'] = NestedArray::mergeDeep([
    'align' => 'left',
  ], $variables['settings']);

  if ($variables['settings']['align'] == 'right') {
    $variables['content_attributes']['class'][] = 'align-right';
  }

  $id = Html::getUniqueId('editorial-hero-slider-slide--' . $variables['id']);
  $variables['attributes']['id'] = $id;

  if (isset($variables['settings']['background'])) {
    $variables['attributes']['style'][] = 'background-image: url(' . $variables['settings']['background'] . ')';
  }

  $variables['#attached']['library'][] = 'editorial/hero-slider';
}

/**
 * Prepares variables for Editorial Slideshow templates.
 *
 * Default template: editorial-slideshow.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - slides: An array of slideshow slides.
 *   - attributes: HTML attributes for the container element.
 *   - content_attributes: HTML attributes for the anchor element.
 *   - settings: An array with settings.
 */
function template_preprocess_editorial_slideshow(&$variables) {
  $id = Html::getUniqueId('editorial-slideshow--' . $variables['id']);

  $variables['settings'] = NestedArray::mergeDeep([
    'dots' => FALSE,
    'arrows' => TRUE,
    'adaptiveHeight' => TRUE,
    'slidesToShow' => 1,
    'autoplay' => FALSE,
    'speed' => 700,
    'infinite' => TRUE,
    'cssEase' => 'ease-out',
  ], $variables['settings']);

  $variables['attributes']['id'] = $id;
  $variables['#attached']['library'][] = 'editorial/slideshow';
  $variables['#attached']['drupalSettings']['editorial']['slideshow'][$id] = $variables['settings'];
}

/**
 * Prepares variables for Editorial Quotation templates.
 *
 * Default template: editorial-quotation.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - content: An array of content.
 *   - attributes: HTML attributes for the container element.
 *   - content_attributes: HTML attributes for the anchor element.
 */
function template_preprocess_editorial_quotation(&$variables) {
  $id = Html::getUniqueId('editorial-quotation--' . $variables['id']);

  $variables['attributes']['id'] = $id;
  $variables['#attached']['library'][] = 'editorial/quotation';
}

/**
 * Prepares variables for Editorial Hero Slider Slide templates.
 *
 * Default template: editorial-hero_slider_slide.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - content: An array with the teaser content.
 *   - attributes: HTML attributes for the container element.
 *   - content_attributes: HTML attributes for the anchor element.
 *   - settings: An array with settings.
 */
function template_preprocess_editorial_slideshow_slide(&$variables) {
  $variables['#attached']['library'][] = 'editorial/slideshow';
}

/**
 * Prepares variables for Editorial Story Teaser templates.
 *
 * Default template: editorial-story-teaser.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - content: An array with the teaser content.
 *   - attributes: HTML attributes for the container element.
 *   - content_attributes: HTML attributes for the anchor element.
 *   - link_attributes: An array with settings.
 */
function template_preprocess_editorial_story_teaser(&$variables) {
  $id = Html::getUniqueId('editorial-story-teaser--' . $variables['id']);
  $variables['attributes']['id'] = $id;
  $variables['#attached']['library'][] = 'editorial/story-teaser';
}

/**
 * Prepares variables for Editorial Teaser templates.
 *
 * Default template: editorial-teaser.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - content: An array with the teaser content.
 *   - attributes: HTML attributes for the container element.
 *   - content_attributes: HTML attributes for the anchor element.
 *   - settings: An array with settings.
 */
function template_preprocess_editorial_teaser(&$variables) {
  $id = Html::getUniqueId('editorial-teaser--' . $variables['id']);
  $variables['attributes']['id'] = $id;
  $variables['#attached']['library'][] = 'editorial/teaser';
}

/**
 * Prepares variables for Editorial YouTube Video templates.
 *
 * Default template: editorial-youtube_video.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - youtube_id: The YouTube Video Id.
 *   - content: An array with the teaser content.
 *   - attributes: HTML attributes for the container element.
 *   - content_attributes: HTML attributes for the anchor element.
 *   - settings: An array with settings.
 */
function template_preprocess_editorial_youtube_video(&$variables) {
  $id = Html::getUniqueId('editorial-youtube-video--' . $variables['id']);
  $variables['attributes']['id'] = $id;
  $variables['#attached']['library'][] = 'editorial/youtube-video';
}
