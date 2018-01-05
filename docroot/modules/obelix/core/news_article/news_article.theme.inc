<?php

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
function template_preprocess_news_article_editorial_youtube_video(&$variables) {
  $variables['attributes']['class'][] = 'first';
}
