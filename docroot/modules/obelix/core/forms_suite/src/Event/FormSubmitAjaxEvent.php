<?php

namespace Drupal\forms_suite\Event;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Form\FormStateInterface;

class FormSubmitAjaxEvent extends FormBaseEvent
{
  /**
   * @var AjaxResponse|NULL
   */
  private $ajax_response;

  /**
   * FormEvent constructor.
   * @param array $form
   * @param FormStateInterface $formState
   * @param AjaxResponse|NULL $ajax_response
   */
  public function __construct(array &$form, FormStateInterface &$formState, AjaxResponse &$ajax_response = NULL)
  {
    parent::__construct($form, $formState);
    $this->ajax_response = &$ajax_response;
  }

  /**
   * @return \Drupal\Core\Ajax\AjaxResponse|NULL
   */
  public function getAjaxResponse()
  {
    return $this->ajax_response;
  }

  /**
   * @param \Drupal\Core\Ajax\AjaxResponse|NULL $ajax_response
   */
  public function setAjaxResponse($ajax_response)
  {
    $this->ajax_response = $ajax_response;
  }

}
