<?php

namespace Drupal\fs_datasource_bridge\EventSubscriber;

use Drupal\Core\Field\FieldItemList;
use Drupal\datasourcehandler\DatasourceService;
use Drupal\forms_suite\DataHandler;
use Drupal\forms_suite\Event\SubmissionProcessEvent;
use Drupal\forms_suite\Event\SubmissionProcessEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class FormSaveSubscriber.
 *
 * @package Drupal\fs_datasource_bridge
 */
class SubmissionSendSubscriber implements EventSubscriberInterface
{
  /**
   * @var DatasourceService
   */
  private $datasource;

  /**
   * Constructs a new FormSaveSubscriber object.
   * @param DatasourceService $datasource
   */
  public function __construct(DatasourceService $datasource)
  {
    $this->datasource = $datasource->get('default');
  }

  /**
   * Send submission to datasource and write result in submission.
   *
   * @param SubmissionProcessEvent $event
   */
  public function datasourceSubmission(SubmissionProcessEvent $event)
  {
    $message = $event->getMessage();
    $form_entity = $event->getForm();
    if (!empty($form_entity->getIncident())) {

      $values = [];

      $data_handler = new DataHandler($message->getFields(), $message);

      foreach ($data_handler->searchFields($message->getFields()) as $field_name => $field) {
        /** @var FieldItemList $field */
        $values[$field_name] = $field->getValue()[0];
      }


      // set tracking
      if (!empty($form_entity->getDesignationID())) {
        $values['field_tracking']['designationID'] = $form_entity->getDesignationID();
      }
      if (!empty($form_entity->getMotivationCode())) {
        $values['field_tracking']['motivationCode'] = $form_entity->getMotivationCode();
      }
      if (!empty($message->getMotivationCode())) {
        $values['field_tracking']['motivationCode'] = $message->getMotivationCode();
      }


      if (!empty($form_entity->getAdditionalTracking())) {
        // have to change to real values
        $values['field_tracking']['productCode'] = $form_entity->getAdditionalTracking();
      }

      // If the message entity provides tracking information use them.
      if ($motivation_code = $message->getMotivationCode()) {
        $values['field_tracking']['motivationCode'] = $motivation_code;
      }
      if ($designation_code = $message->getDesignationCode()) {
        $values['field_tracking']['designationID'] = $designation_code;
      }
      if ($additional_tracking = $message->getAdditionalTracking()) {
        $values['field_tracking']['productCode'] = $additional_tracking;
      }

      $incident_type = $data_handler->incidentConditions($form_entity->getIncident());

      $transfer_id = $this->datasource->getIncidentClass()->setIncident(
        $values,
        $incident_type,
        $message->id(),
        $form_entity->label(),
        $message->getReferencedEntity() ? $message->getReferencedEntity()
          ->id() : NULL
      );

      // save the transfer id (for example WebReferenceID)
      // if process failed save -1
      if (is_numeric($transfer_id)) {
        $message->setTransferID($transfer_id);
        \Drupal::logger('forms')
          ->notice('Message ID: ' . $message->id() . ' send to iVision.');
      } else {
        $message->setTransferID(-1);
      }
    }

     $event->setMessage($message);
     $event->setForm($form_entity);

  }


  /**
   * {@inheritdoc}
   */
  static function getSubscribedEvents()
  {
    if (defined('Drupal\forms_suite\Event\SubmissionProcessEvents::PROCESS_PRE_SAVE')) {
      $events[SubmissionProcessEvents::PROCESS_PRE_SAVE][] = ['datasourceSubmission', 1000];
      return $events;
    }
  }


}
