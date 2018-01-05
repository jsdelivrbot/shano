<?php

namespace Drupal\giftshop\EventSubscriber;

use Drupal\contact\Entity\Message;
use Drupal\Core\Field\FieldItemList;
use Drupal\file\Entity\File;
use Drupal\forms_suite\Event\MailHandlerEvent;
use Drupal\forms_suite\Event\MailHandlerEvents;
use Drupal\giftshop\GiftshopPdfGeneratorInterface;
use Drupal\node\Entity\Node;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


/**
 * Class MailHandlerSubscriber.
 *
 * @package Drupal\giftshop
 */
class MailHandlerSubscriber implements EventSubscriberInterface
{
  /**
   * @var GiftshopPdfGeneratorInterface
   */
  private $giftshop_pdf_generator;

  /**
   * MailHandlerSubscriber constructor.
   * @param GiftshopPdfGeneratorInterface $giftshop_pdf_generator
   */
  public function __construct(GiftshopPdfGeneratorInterface $giftshop_pdf_generator)
  {
    $this->giftshop_pdf_generator = $giftshop_pdf_generator;
  }

  /**
   * Mail handler pre process params.
   *
   * @param MailHandlerEvent $event
   */
  public function mailHandlerPreProcessParams(MailHandlerEvent $event)
  {

    $data_handler = $event->getDataHandler();
    $message = $event->getMessage();
    $values = $event->getValues();

    // giftshop
    if (isset($values['field_giftshop'])) {

      $gifts_values = [];
      $gifts = [];

      foreach ($message->getEntityChildren() as $reference_message) {
        /** @var Message $reference_message */
        /** @var FieldItemList $reference_gift */
        if ($reference_gift = $reference_message->get('field_giftshop')) {
          $gift_value = $reference_gift->getValue();
          $gifts_values[] = unserialize($gift_value[0]['gifts']);
        }
      }

      $gifts_values[] = unserialize($values['field_giftshop']['gifts']);

      foreach ($gifts_values as $gift_key => $gift) {
        $url = NULL;
        if ($node = Node::load($gift['node_id'])) {
          $gifts[$gift_key]['name'] = $node->label();
          $gifts[$gift_key]['amount'] = (int)$node->get('field_gift_price')->value * $gift['quantity'];
          $gifts[$gift_key]['direct_pdf_link'] = '';

          switch ($gift['response_type']) {
            case 'certificate':
              $data = $gift['response_data'];
              if ($data['send_type'] == 'email') {
                if ($node->bundle() === 'gift_type' && !$node->field_gift_certificate->isEmpty()) {
                  if ($file = File::load($node->field_gift_certificate[0]->target_id)) {
                    /** @var File $generated_file */
                    $generated_file = \Drupal::service('giftshop.pdf_generator')
                      ->generate($file, $data['send_email']['from'], $data['send_email']['to']);
                    $gifts[$gift_key]['direct_pdf_link'] = file_create_url($generated_file->getFileUri());
                  }
                }
              }
              break;
          }
        }
      }

      $gifts_printed = '';
      $gifts_amount = 0;

      foreach ($gifts as $gift) {
        $gifts_amount += (int)$gift['amount'];
        $gifts_printed .= $gift['name'] . ' (' . $gift['amount'] . '€). ';

        if (!empty($gift['direct_pdf_link'])) {
          $gift['direct_pdf_link'] = str_replace('..', '.', $gift['direct_pdf_link']);
          $gifts_printed .= t('Downloadlink: ') . $gift['direct_pdf_link'];
        }

        $gifts_printed .= PHP_EOL;
      }

      $data_handler->setPlaceholderValues([
        'wovi_giftshop' => [
          'gifts_list' => $gifts_printed,
          'gift_amount_complete' => $gifts_amount,
        ],
      ]);

    }

    $event->setDataHandler($data_handler);
    $event->setMessage($message);
  }

  /**
   * {@inheritdoc}
   */
  static function getSubscribedEvents()
  {
    if (defined('Drupal\forms_suite\Event\MailHandlerEvents::MAIL_MESSAGE_PRE_PROCESS_PARAMS')) {
      $events[MailHandlerEvents::MAIL_MESSAGE_PRE_PROCESS_PARAMS][] = ['mailHandlerPreProcessParams', 1000];
      return $events;
    }
  }

}
