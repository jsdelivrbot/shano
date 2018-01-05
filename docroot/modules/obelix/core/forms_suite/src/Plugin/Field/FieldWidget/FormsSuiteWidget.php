<?php

namespace Drupal\forms_suite\Plugin\Field\FieldWidget;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\BeforeCommand;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Ajax\RemoveCommand;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;


/**
 * Basse class for Forms suite Widgets.
 */
abstract class FormsSuiteWidget extends WidgetBase
{

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state){
    $element = [];
    $forms_suite_configs = $this->getFieldSetting('forms_suite_configs');
    if(!empty($forms_suite_configs['headline'])){
      $element['headline'] = [
        '#markup' => t($forms_suite_configs['headline']),
      ];
    }
    if(!empty($forms_suite_configs['subline'])){
      $element['subline'] = [
        '#markup' => t($forms_suite_configs['subline']),
      ];
    }

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function form(FieldItemListInterface $items, array &$form, FormStateInterface $form_state, $get_delta = NULL)
  {

    // cut _widget form the base id.
    $theme = 'form_section__'.substr($this->getBaseId(),0, -7);

    $form_parent = parent::form($items, $form, $form_state, $get_delta);
    $form_parent['widget']['#theme'] = $theme;
    $form_parent['widget']['#cache']['max-age'] = 0;
    $form_parent['widget']['#elements'] = $form_parent['widget'];

    return $form_parent;
  }

  /**
   * Form validation handler for widget elements.
   *
   * @param array $element
   *   The form element.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   */
  public static function validateElement(array $element, FormStateInterface &$form_state)
  {

    $form_state->setLimitValidationErrors(NULL);
    $trigger_element = $form_state->getTriggeringElement();

    $error = $form_state->getError($trigger_element);
    if ($error !== NULL) {
      $form_state->setRebuild(TRUE);
    } else {
      $form_state->setRebuild(FALSE);
    }

  }

  /**
   * Ajax callback to validate the donation form.
   */
  public static function validateFormAjax(array &$form, FormStateInterface $form_state)
  {
    $trigger_element = $form_state->getTriggeringElement();
    $trigger_input_id = $trigger_element['#attributes']['data-drupal-selector'];

    $response = new AjaxResponse();

    $error = $form_state->getError($trigger_element);
    if ($error !== NULL) {
      $error_render_array = [
        '#theme' => 'status_messages',
        '#message_list' => array('error' => array($error)),
        '#status_headings' => [
          'status' => t('Status message'),
          'error' => t('Error message'),
          'warning' => t('Warning message'),
        ],
      ];

      $response->addCommand(new RemoveCommand('#error-message-' . $trigger_input_id));
      $response->addCommand(new BeforeCommand('#' . $trigger_input_id, '<div class="error-message" id="error-message-' . $trigger_input_id . '"></div>'));
      $response->addCommand(new HtmlCommand('#error-message-' . $trigger_input_id, $error_render_array));
    } else {
      $response->addCommand(new RemoveCommand('#error-message-' . $trigger_input_id));
      $response->addCommand(new InvokeCommand('#' . $trigger_input_id, "addClass", ['validated']));
    }

    if($trigger_element['#type'] != 'submit'){
      drupal_get_messages('error');
    }

    return $response;
  }

}
