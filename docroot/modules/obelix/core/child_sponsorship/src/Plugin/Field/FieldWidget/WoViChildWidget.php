<?php

namespace Drupal\child_sponsorship\Plugin\Field\FieldWidget;

use Drupal\child\Controller\ChildController;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\forms_suite\Plugin\Field\FieldWidget\FormsSuiteWidget;
use Drupal\Core\Entity\ContentEntityForm;

/**
 * Plugin implementation of the 'wovi_child_widget' widget.
 *
 * @FieldWidget(
 *   id = "wovi_child_widget",
 *   multiple_values = TRUE,
 *   label = @Translation("Forms suite child widget"),
 *   field_types = {
 *     "wovi_child_field"
 *   }
 * )
 */
class WoViChildWidget extends FormsSuiteWidget
{
  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state)
  {

    $element = parent::formElement($items, $delta, $element, $form, $form_state);

    $form_object = $form_state->getFormObject();

    $is_hidden_child_form = FALSE;

    // Check whether this widget should have especial behaviour in regard to hidden child logic.
    if ($form_object instanceof ContentEntityForm) {
      $is_hidden_child_form = $form_object->getEntity()->bundle() == 'sponsor_child';
    }

    // get Child and Check if it is blocked
    $child_controller = new ChildController();

    if (!($child = $child_controller->getChildFromUser(FALSE, $is_hidden_child_form))) {
      if (!$is_hidden_child_form) {
        $child = $child_controller->getChildFromUser(TRUE);

        $element['child_blocked_info'] = [
          '#markup' => $this->t('This child is currently blocked by another user. We selected a new child for you.'),
        ];
      }
    }

    // Prevent fatal errors if by some reason child wasn't loaded.
    if (!$child) {
      $element['child_blocked_info'] = [
        '#markup' => $this->t('Sorry this child is not available anymore.'),
      ];

      return $element;
    }

    $image = $child_controller->getChildImage($child, "square_xs");

    // Do not block children for hidden pages.
    if (!$is_hidden_child_form) {
      $child->blockChildInForm();
      $child_controller->setBlockedChildForUser($child);
    }

    $element['child']['image'] = [
      '#type' => 'item',
      '#markup' => $image,
    ];

    $element['child']['givenname'] = [
      '#markup' => $child->getGivenName()->value,
    ];
//    $element['child']['familyname'] = [
//      '#markup' => $child->getFamilyName()->value,
//    ];

    if (!empty($child->getBirthdate()->value)) {
      $element['child']['birthdate'] = [
        '#markup' => \Drupal::service('date.formatter')
          ->format(strtotime($child->getBirthdate()->value), 'custom', 'd.m.Y'),
      ];
    }

    $country = $child->getCountry();

    $element['child']['country'] = [
      '#markup' => $country ? $country->label() : '',
    ];

    $element['childSequenceNo'] = [
      '#type' => 'hidden',
      '#value' => $child->getUniqueSequenceNumber(),
    ];

    $element['childCountryCode'] = [
      '#type' => 'hidden',
      '#value' => $country ? $country->getCountryCode() : NULL,
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public static function validateElement(array $element, FormStateInterface &$form_state)
  {
    // validate your fields.
    parent::validateElement($element, $form_state);
  }

}
