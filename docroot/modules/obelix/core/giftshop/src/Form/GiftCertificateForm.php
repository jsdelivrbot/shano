<?php

/**
 * @file
 * Contains Drupal\giftshop\Form\GiftCertificateForm
 */

namespace Drupal\giftshop\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Url;
use Drupal\file\Entity\File;
use Drupal\forms_suite\Plugin\Field\FieldWidget\WoViUserDataWidget;
use Drupal\giftshop\Controller\CartController;
use Drupal\giftshop\GiftshopCartTempItemInterface;
use Drupal\node\Entity\Node;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class GiftCertificateForm.
 *
 * @package Drupal\giftshop\Form
 */
class GiftCertificateForm extends FormBase {

  /**
   * @var GiftshopCartTempItemInterface
   */
  protected $giftshopCartTempItem;

  /**
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   * @return \Drupal\giftshop\Form\GiftCertificateForm
   */
  public static function create(ContainerInterface $container) {
    return new self($container->get('giftshop.cart.temp'));
  }

  /**
   * GiftCertificateForm constructor.
   * @param \Drupal\giftshop\GiftshopCartTempItemInterface $giftshopCartTempItem
   */
  public function __construct(GiftshopCartTempItemInterface $giftshopCartTempItem) {
    $this->giftshopCartTempItem = $giftshopCartTempItem;
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return array(
      'giftshop.gift.certificate.form',
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'giftshop_gift_certificate_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['#theme'] = 'giftshop_gift_certificate_form';

    $form['#attached']['library'][] = 'beaufix/giftshop';
    $form['#attached']['library'][] = 'giftshop/forms';

    if ($node_id = $this->giftshopCartTempItem->getNodeId()) {
      $node = Node::load($node_id);

      $form['certificate'] = [];

      if ($target_id = $node->field_gift_certificate_preview[0]->target_id) {
        $form['certificate']['image'] = CartController::buildRenderableImage(File::load($target_id));
      }

      $send_type_condition = array(
        'visible' => array(
          ':input[name="send_type"]' => array('value' => 'email'),
        ),
      );

      $form['send_type'] = [
        '#type' => 'radios',
        '#title' => $this->t('Card type'),
        '#options' => [
          'post' => $this->t('Send by post'),
          'email' => $this->t('Send by e-mail'),
        ],
        '#default_value' => 'post',
      ];

      $form['send_email'] = [
        '#type' => 'container',
        '#tree' => TRUE,
      ];

      $form['send_email']['to'] = array(
        '#type' => 'textfield',
        '#title' => t('Send to'),
        '#states' => $send_type_condition,
        '#attributes' => [
          'maxlength' => 30,
        ]
      );

      $form['send_email']['from'] = array(
        '#type' => 'textfield',
        '#title' => t('Send from'),
        '#states' => $send_type_condition,
        '#attributes' => [
          'maxlength' => 30,
        ]
      );

      $form['submit'] = array(
        '#type' => 'submit',
        '#value' => t('Add to gift basket'),
        '#attributes' => [
          'class' => ['btn btn-beauty btn-xl btn-fullsize-xs']
        ],
      );
    }
    else {
      return new RedirectResponse(Url::fromUri('internal:/node/625')
        ->toString());
    }

    return $form;
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
    $tempItem = \Drupal::service('giftshop.cart.temp')
      ->setResponseType('certificate')
      ->setResponseData($form_state->cleanValues()->getValues());

    if (\Drupal::service('giftshop.cart')
      ->addItem($tempItem->getStorableItem()) !== FALSE
    ) {
      $tempItem->reset();
      drupal_set_message($this->t('Certificate successfully added to your cart'));
    }
    else {
      drupal_set_message($this->t('Something went wrong please try again.'), 'error');
    }

    $form_state->setRedirect('giftshop.cart');
  }

}
