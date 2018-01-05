<?php

namespace Drupal\giftshop\EventSubscriber;

use Drupal\affiliate\Plugin\Field\FieldType\TrackingItem;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\forms_suite\DataHandler;
use Drupal\forms_suite\Entity\Message;
use Drupal\forms_suite\Event\FormEvents;
use Drupal\forms_suite\Event\FormSaveEvent;
use Drupal\giftshop\GiftshopCartInterface;
use Drupal\node\Entity\Node;
use Drupal\replicate\Replicator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


/**
 * Class FormSaveSubscriber.
 *
 * @package Drupal\giftshop
 */
class FormSaveSubscriber implements EventSubscriberInterface {


  /**
   * Set affiliate tracking data.
   *
   * @param FormSaveEvent $event
   */
  public function formPostSaveSubmission(FormSaveEvent $event){
    $message = $event->getMessage();

    $data_handler = new DataHandler($message->getFields(), $message, ['empty' => []]);
    $values = $data_handler->getFields();

    // giftshop
    if (isset($values['field_giftshop'])) {

      $gifts = unserialize($values['field_giftshop']['gifts']);

      $number_of_gifts = count($gifts);

      // get first gift key and update giftshop data with first gift.
      reset($gifts);
      $first_gift_id = key($gifts);

      /** @var Node $gift_node */
      $gift_node = Node::load($gifts[$first_gift_id]['node_id']);
      $values['field_giftshop']['gifts'] = serialize($gifts[$first_gift_id]);
      $values['field_giftshop']['gift_price'] = (int) $gift_node->get('field_gift_price')->value * $gifts[$first_gift_id]['quantity'];

      unset($gifts[$first_gift_id]);
      $message->set('field_giftshop', $values['field_giftshop']);

      // Set tracking information.
      $this->setTrackingFromGiftNodes($message);
      $message->save();

      // if there are more than one gift is ordered multi iVision calls have to be created.
      if ($number_of_gifts > 1) {

        foreach ($gifts as $gift_id => $gift) {

          /** @var Node $gift_node */
          $gift_node = Node::load($gift['node_id']);
          /** @var Replicator $replicator */
          $replicator = \Drupal::service('replicate.replicator');
          /** @var Message $message_entity */
          $message_entity = $replicator->cloneEntity($message);
          $values['field_giftshop']['gifts'] = serialize($gift);
          $values['field_giftshop']['gift_price'] = (int) $gift_node->get('field_gift_price')->value * $gift['quantity'];
          $message_entity->set('field_giftshop', $values['field_giftshop']);
          $message_entity->setReference($message->id());

          // Clear Motivation code history.
          $message_entity->clearTrackingParameter();
          // Restore original tracking because we clone from the previous
          // iteration which could have overriden tracking from gift nodes.
          $this->setTrackingParameters($message_entity);
          // Override tracking from gits.
          $this->setTrackingFromGiftNodes($message_entity);
          // Save message entity.
          $message_entity->save();

          // Let other modules handle checkout gifts.
          \Drupal::moduleHandler()->invokeAll('giftshop_gift_checkout', [$message_entity, $event->getForm()['#form_id']]);
        }
      }

      /** @var GiftshopCartInterface $cart_service */
      \Drupal::service('giftshop.cart')->emptyCart();
    }

    $event->setMessage($message);

  }


  /**
   * @param Message $message
   */
  protected function setTrackingFromGiftNodes(Message $message)
  {
    /**
     * @var string $field_name
     *   The machine name of the field.
     * @var FieldDefinitionInterface $field_definition
     *  The field definition.
     */
    foreach ($message->getFieldDefinitions() as $field_name => $field_definition) {
      if ($field_definition->getType() == 'wovi_giftshop_field') {
        $value = $message->get($field_definition->getName())->getValue();
        $value = unserialize($value[0]['gifts']);

        if (isset($value['node_id'])) {
          /** @var \Drupal\node\Entity\Node $gift */
          $gift = \Drupal::entityTypeManager()
            ->getStorage('node')
            ->load($value['node_id']);

          $this->doSetTrackingFromGiftNodes($message, $gift);
        }
      }
    }
  }

  /**
   * @param Message $message
   * @param Node $gift
   */
  protected function doSetTrackingFromGiftNodes(Message &$message, Node $gift)
  {
    /** @var FieldDefinitionInterface $field_definition */
    foreach ($gift->getFieldDefinitions() as $field_definition) {
      if ($field_definition->getType() == 'tracking') {
        /** @var TrackingItem $field */
        $field = $gift->get($field_definition->getName());

        if (!$field->isEmpty()) {
          // Override message tracking with gift item tracking if provided.
          if ($motivationCode = $field[0]->getMotivationCode()) {
            $message->setMotivationCode($motivationCode);
          }
          if ($designation_code = $field[0]->getDesignationCode()) {
            $message->setDesignationCode($designation_code);
          }
          if ($additional_tracking = $field[0]->getAdditionalTracking()) {
            $message->setAdditionalTracking($additional_tracking);
          }
        }
      }
    }
  }

  /**
   * @param Message $message
   */
  protected function setTrackingParameters(\Drupal\forms_suite\Entity\Message &$message) {
    // Setting motivation code from
    if ($motivation_code = $message->getForm()->getMotivationCode()) {
      $message->setMotivationCode($motivation_code);
    }

    // Override tracking by affiliate system.
    if ($affiliateItem = \Drupal::service('affiliate_session_manager')->restoreItem()) {
      $message->setAffiliateID($affiliateItem->id());
      if ($motivation_code = $affiliateItem->getMotivationCode()) {
        $message->setMotivationCode($motivation_code);
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  static function getSubscribedEvents()
  {
    if (defined('Drupal\forms_suite\Event\FormEvents::SUBMITTED_POST_SAVE')) {
      $events[FormEvents::SUBMITTED_POST_SAVE][] = ['formPostSaveSubmission', 1000];
      return $events;
    }
  }




}
