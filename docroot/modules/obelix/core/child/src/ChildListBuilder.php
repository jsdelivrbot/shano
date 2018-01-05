<?php

namespace Drupal\child;

use Drupal\Core\Datetime\DateFormatter;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Render\Element\RenderElement;
use Drupal\Core\Render\MainContent\HtmlRenderer;
use Drupal\Core\Routing\LinkGeneratorTrait;
use Drupal\file\Entity\File;

/**
 * Defines a class to build a listing of Child entities.
 *
 * @ingroup child
 */
class ChildListBuilder extends EntityListBuilder
{
  use LinkGeneratorTrait;

  /**
   * {@inheritdoc}
   */
  public function buildHeader()
  {
    $header['image'] = $this->t('Child Image');
    $header['sequence_id'] = $this->t('Sequence Number');
    $header['ivisionid'] = $this->t('iVision ID');
    $header['name'] = $this->t('Name');
    $header['birthdate'] = $this->t('Birth date');
    $header['status'] = $this->t('Status');
    $header['block_time'] = $this->t('Block time');
    $header['blocked_from'] = $this->t('Blocked from');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity)
  {

    // render the child image
    $file = File::load($entity->get('field_child_image')->getValue()[0]['target_id']);

    if ($file && ($uri = $file->getFileUri())) {
      $variables = array(
        'style_name' => 'thumbnail',
        'uri' => $uri,
      );

      // The image.factory service will check if image is valid.
      $image = \Drupal::service('image.factory')->get($uri);

      if ($image->isValid()) {
        $variables['width'] = $image->getWidth();
        $variables['height'] = $image->getHeight();
      }
      else {
        $variables['width'] = $variables['height'] = NULL;
      }

      $image_render_array = [
        '#theme' => 'image_style',
        '#width' => $variables['width'],
        '#height' => $variables['height'],
        '#style_name' => $variables['style_name'],
        '#uri' => $variables['uri'],
      ];

      /* @var $entity \Drupal\child\Entity\Child */
      $row['image'] = \Drupal::service('renderer')->render($image_render_array);
    }
    else {
      $row['image'] = $this->t('No image');
    }

    if ($entity->getCountry()) {
      $country_id = $entity->getCountry()->getCountryCode();
    }
    else {
      // Avoid fatal errors in case of content problems.
      $country_id = 'none';
    }

    $sequence_number = $country_id . '-' . $entity->getUniqueSequenceNumber();

    $row['sequence_id'] = $sequence_number;
    $row['ivisionid'] = $entity->get('ivision_id')->value;
    $row['name'] = $entity->get('field_child_givenname')
        ->value . " " . $entity->get('field_child_familyname')->value;
    $row['birthdate'] = $entity->get('field_child_birthdate')->value;

    switch ($entity->get('status')->value) {
      case 0 :
        $row['status'] = $this->t('Found sponsorship');
        break;
      case 1 :
        $row['status'] = $this->t('Available');
        break;
      case 2 :
        $row['status'] = $this->t('Blocked');
        break;
      default:
        $row['status'] = $this->t('Unknown');
    }

    $block_timestamp = (int)$entity->get('block_time')->value;

    if($block_timestamp > 0){
      /** @var DateFormatter $block_time */
      $block_time = \Drupal::service('date.formatter')
        ->format($block_timestamp, 'short');
    }
    else{
      $block_time = '-';
    }

    $row['block_time'] = $block_time;

    if($entity->getBlockedFrom() !== NULL){
      $row['blocked_from'] = $entity->getBlockedFrom()->value;
    }
    else{
      $row['blocked_from'] = '-';
    }

    return $row + parent::buildRow($entity);
  }

}
