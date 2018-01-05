<?php

namespace Drupal\child_sponsorship\EventSubscriber;

use Drupal\child\Controller\ChildController;
use Drupal\child\Entity\Child;
use Drupal\Core\Field\FieldItemList;
use Drupal\forms_suite\DataHandler;
use Drupal\forms_suite\Event\FormEvents;
use Drupal\forms_suite\Event\FormSaveEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


/**
 * Class FormSaveSubscriber.
 *
 * @package Drupal\child_sponsorship
 */
class FormSaveSubscriber implements EventSubscriberInterface
{
  /**
   * Set affiliate tracking data.
   *
   * @param FormSaveEvent $event
   */
  public function formPostSaveSubmission(FormSaveEvent $event)
  {

    $message = $event->getMessage();

    $data_handler = new DataHandler($message->getFields(), $message, ['empty' => []]);
    $values = $data_handler->getFields();

    // block child after email suggestion
    $child_entity_type = \Drupal::entityTypeManager()->getStorage('child');
    if (isset($values['field_suggestion']) && $values['field_suggestion']['suggestion'] == 1) {
      // set child to be blocked after successful suggestion form submit.
      // split sequence number for the direct child call.
      $parts = explode('-', $values['field_suggestion']['childSequenceNo']);
      $children = $child_entity_type->loadByProperties([
        'field_child_project' => $parts[0],
        'field_child_childsequence' => $parts[1],
      ]);
      foreach ($children as $child) {
        /** @var Child $child */
        $child->blockChildForSuggestion();
        $blocked_from = 'suggestion';
        foreach ($values as $field) {
          foreach ($field as $item_name => $item) {
            if ($item_name == 'email') {
              $blocked_from = $item;
            }
          }
        }
        $child->setBlockedFrom($blocked_from);
        $child->save();
      }
    }

    if (!empty($values['field_child']['childSequenceNo'])) {

      // load child, remove child for user and set to founded
      $parts = explode('-', $values['field_child']['childSequenceNo']);

      // Avoid exceptions on data without all needed fields filled.
      if (!empty($parts[1])) {
        $children = $child_entity_type->loadByProperties([
          'field_child_project' => $parts[0],
          'field_child_childsequence' => $parts[1],
        ]);

        $child_controller = new ChildController();

        foreach ($children as $child) {
          $child_controller->removeChildForUser($child);
          $child->setFoundSponsorship();
          $child->save();
        }
      }
    }
  }


  /**
   * {@inheritdoc}
   */
  static function getSubscribedEvents()
  {
    if (defined('Drupal\forms_suite\Event\FormEvents::SUBMITTED_POST_SAVE')){
      $events[FormEvents::SUBMITTED_POST_SAVE][] = ['formPostSaveSubmission', 900];
      return $events;
    }
  }


}
