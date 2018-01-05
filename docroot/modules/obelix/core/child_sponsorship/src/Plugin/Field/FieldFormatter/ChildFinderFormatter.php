<?php

namespace Drupal\child_sponsorship\Plugin\Field\FieldFormatter;

use Drupal\child\Controller\ChildController;
use Drupal\child_sponsorship\Plugin\Field\FieldType\ChildFinderItem;
use Drupal\Component\Utility\Html;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'child finder' formatter.
 *
 * @FieldFormatter(
 *   id = "child_finder_formatter",
 *   label = @Translation("child finder formatter"),
 *   field_types = {
 *     "child_finder_item"
 *   }
 * )
 */
class ChildFinderFormatter extends FormatterBase
{

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode)
  {
    $elements = [];

    /** @var ChildFinderItem $child_finder */
    $child_finder_values = $items->getValue()[0];

    $child_controller = new ChildController();
    $child = $child_controller->getRandomChild([
      'gender' => $child_finder_values['child_gender'],
      'country' => $child_finder_values['child_country'],
    ]);

    $image = $child_controller->getChildImage($child, "square_xs");
    $elements[] = ['#markup' => $image];

    return $elements;
  }

  /**
   * Generate the output appropriate for one field item.
   *
   * @param \Drupal\Core\Field\FieldItemInterface $item
   *   One field item.
   *
   * @return string
   *   The textual output generated.
   */
  protected function viewValue(FieldItemInterface $item)
  {
    // The text value has no text format assigned to it, so the user input
    // should equal the output, including newlines.
    return nl2br(Html::escape($item->value));
  }

}
