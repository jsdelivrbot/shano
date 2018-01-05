<?php

namespace Drupal\fs_payment\EventSubscriber;

use Drupal\Core\Field\FieldItemList;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Drupal\Core\Url;
use Drupal\datasourcehandler\DatasourceService;
use Drupal\forms_suite\DataHandler;
use Drupal\forms_suite\Event\FormEvents;
use Drupal\forms_suite\Event\FormSaveEvent;
use Drupal\payment_gateway\Plugin\PaymentGatewayBase;
use Drupal\payment_gateway\Plugin\PaymentGatewayManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class FormSaveSubscriber.
 *
 * @package Drupal\fs_payment\EventSubscriber
 */
class FormSaveSubscriber implements EventSubscriberInterface
{
  /**
   * @var DatasourceService
   */
  private $session;

  /**
   * Constructs a new FormSaveSubscriber object.
   * @param Session $session
   */
  public function __construct(Session $session)
  {
    $this->session = $session;
  }

  /**
   * Send submission to datasource and write result in submission.
   *
   * @param FormSaveEvent $event
   */
  public function formPreSaveSubmission(FormSaveEvent $event)
  {
    $form_state = $event->getFormState();
    $message = $event->getMessage();
    $form_entity = $message->getForm();

    $values = [];
    $data_handler = new DataHandler($message->getFields(), $message, ['empty' => []]);

    foreach ($data_handler->searchFields($message->getFields()) as $field_name => $field) {
      /** @var FieldItemList $field */
      $values[$field_name] = $field->getValue()[0];
    }

    /** @var PaymentGatewayManager $plugin_manager */
    $plugin_manager = \Drupal::service('plugin.manager.payment_gateway');
    $definitions = $plugin_manager->getDefinitions();
    $gateway_options = $plugin_manager->getPaymentGatewayOptions();
    $payment_method = $values['field_payment_method']['paymentMethod'];

    if(isset($gateway_options[$payment_method])){
      $definition = $definitions[$payment_method];
      /** @var PaymentGatewayBase $instance */
      $instance = $plugin_manager->getInstance(['id' => $definition['id']]);

      $args = [
        'message' => $message,
        'form_entity' => $form_entity,
        'values' => $values,
      ];

      if ($external_link = $instance->externalPaymentLink($args)) {
        $form_id = $form_entity->getOriginalId();
        $this->session->set('form', [
          $form_id => [
            'external' => $message->get('uuid')->getString(),
          ]
        ]);
        $form_state->setRedirectUrl(Url::fromUri($external_link));

        // set external payment process to open
        $message->set('external_payment', 2);
        // external payment process.
        $message->setTransferID(-2);

      }
    }

    $event->setFormState($form_state);
    $event->setMessage($message);
  }


  /**
   * {@inheritdoc}
   */
  static function getSubscribedEvents()
  {
    if (defined('Drupal\forms_suite\Event\FormEvents::SUBMITTED_PRE_SAVE')) {
      $events[FormEvents::SUBMITTED_PRE_SAVE][] = ['formPreSaveSubmission', 1000];
      return $events;
    }
  }


}
