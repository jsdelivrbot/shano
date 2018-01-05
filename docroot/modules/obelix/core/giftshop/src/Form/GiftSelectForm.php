<?php

/**
 * @file
 * Contains Drupal\giftshop\Form\GiftSelect
 */

namespace Drupal\giftshop\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\RedirectCommand;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Url;
use Drupal\file\Entity\File;

/**
 * Class ContactForm.
 *
 * @package Drupal\giftshop\Form
 */
class GiftSelectForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return array(
      'giftshop.gift.select.form',
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'giftshop_gift_select_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['#theme'] = 'giftshop_gift_select_form';
    $form['#attached']['library'][] = 'giftshop/forms';

    $build_info = $form_state->getBuildInfo();
    $gift_node = $build_info['args'][0];

    if ($gift_node instanceof ContentEntityInterface) {
      $form['id'] = [
        '#type' => 'value',
        '#value' => $gift_node->id(),
      ];

      $form['quantity'] = [
        '#type' => 'number',
        '#min' => 1,
        '#required' => TRUE,
        '#title' => $this->t('Quantity'),
        '#default_value' => 1,
      ];

      $form['actions'] = ['#type' => 'actions'];
      $form['actions']['submit'] = [
        '#type' => 'submit',
        '#value' => t('Select gift'),
        '#attributes' => [
          'class' => ['btn btn-beauty btn-transparent btn-fullsize-xs']
        ],
      ];

      // If offcanvas module is enabled open destination in offcanvas.
      if (\Drupal::moduleHandler()->moduleExists('offcanvas')) {
//        $form_state->set('current_url', $gift_node->toUrl());
//
//        $form['actions']['submit']['#ajax'] = [
//          'callback' => '::openOffcanvasAjax',
//        ];
      }

    }
    else {
      $form['error'] = [
        '#markup' => $this->t('This item is currently not available.'),
      ];
    }

    return $form;
  }

  /**
   * Ajax callback to open redirect destination in offcanvas.
   *
   * @param array $form
   *   The form array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The Form State object.
   *
   * @return \Drupal\Core\Ajax\AjaxResponse
   *    A ajax response with redirect command to open the current url with the
   *    offcanvas destination as fragment on it.
   */
  public static function openOffcanvasAjax(array $form, FormStateInterface $form_state) {
    $response = new AjaxResponse();

    /**
     * @var Url $source
     */
    $source = $form_state->get('current_url');
    $destination = Url::fromRoute('giftshop.cart.add');
    $source->setOption('fragment', 'canvas=' . $destination->toString());

    $response->addCommand(new RedirectCommand($source->toString()));

    return $response;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    if ($id = $form_state->getValue('id')) {

      \Drupal::service('giftshop.cart.temp')
        ->reset()
        ->setNodeId($id)
        ->setQuantity($form_state->getValue('quantity', 1));

      $form_state->setRedirect('giftshop.cart.add');
    }
  }
}
