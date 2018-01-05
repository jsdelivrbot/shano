<?php

namespace Drupal\fs_datasource_bridge\EventSubscriber;

use Drupal\forms_suite\Event\FormEditEvent;
use Drupal\forms_suite\Event\FormEditEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class FormEditSubscriber.
 *
 * @package Drupal\fs_datasource_bridge
 */
class FormEditSubscriber implements EventSubscriberInterface
{

  /**
   * Extend the Edit form with the incident field.
   *
   * @param FormEditEvent $event
   */
  public function formEditBuild(FormEditEvent $event)
  {
    $form = $event->getForm();
    $form_entity = $event->getFormEntity();

    $form['incident'] = array(
      '#type' => 'textfield',
      '#title' => t('Incident type'),
      '#default_value' => $form_entity->getIncident(),
      '#description' => t("iVision tracking incident type"),
      '#weight' => 40,
    );

    $event->setForm($form);
  }


  /**
   * {@inheritdoc}
   */
  static function getSubscribedEvents()
  {
    if (defined('Drupal\forms_suite\Event\FormEditEvents::FORM_EDIT_BUILD')) {
      $events[FormEditEvents::FORM_EDIT_BUILD][] = ['formEditBuild', 1000];
      return $events;
    }
  }


}
