<?php

/**
 * @file
 * Contains Drupal\giftshop\Form\GiftCardForm
 */

namespace Drupal\giftshop\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormBase;
use Drupal\forms_suite\Plugin\Field\FieldWidget\WoViUserDataWidget;
use Drupal\giftshop\GiftshopCartTempItemInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Url;

/**
 * Class ContactForm.
 *
 * @package Drupal\giftshop\Form
 */
class GiftCardForm extends FormBase {

  /**
   * @var GiftshopCartTempItemInterface
   */
  protected $giftshopCartTempItem;

  /**
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   * @return \Drupal\giftshop\Form\GiftCardForm
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
      'giftshop.gift.card.form',
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'giftshop_gift_card_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = [];
    $form['#theme'] = 'giftshop_gift_card_form';
    $form['#attached']['library'][] = 'beaufix/giftshop';

    if ($node_id = $this->giftshopCartTempItem->getNodeId()) {

      $form['card_type'] = [
        '#type' => 'radios',
        '#title' => $this->t('Select theme'),
        '#options' => [
          $this->t('Neutral'),
          $this->t('All the best'),
          $this->t('Birthday'),
        ],
        '#default_value' => 0
      ];

      // @todo set as taxonomy
      $form['card_from_greetings'] = [
        '#type' => 'select',
        '#options' => [
          'Dein' => 'Dein',
          'Deine' => 'Deine',
          'Euer' => 'Euer',
          'Eure' => 'Eure',
          'Ihr' => 'Ihr',
          'Ihre' => 'Ihre',
        ],
      ];

      $form['card_from'] = [
        '#type' => 'textfield',
        '#title' => t('Name'),
        '#attributes' => [
          'maxlength' => 30,
        ]
      ];

      // @todo set as taxonomy
      $form['card_to_salutation'] = [
        '#type' => 'select',
        '#options' => [
          'Liebe' => 'Liebe',
          'Lieber' => 'Lieber',
          'Liebes' => 'Liebes',
        ],
      ];

      $form['card_to'] = [
        '#type' => 'textfield',
        '#title' => t('Name'),
        '#attributes' => [
          'maxlength' => 30,
        ]
      ];

      $form['card_message'] = [
        '#type' => 'textarea',
        '#title' => t('Personal message'),
        '#maxlength_js' => TRUE,
        '#attributes' => [
          'maxlength' => 700,
        ]
      ];

      $form['delivery_address'] = [
        '#type' => 'radios',
        '#title' => $this->t('Delivery address'),
        '#options' => [
          $this->t('To invoice address'),
          $this->t('Other shipping address'),
        ],
        '#default_value' => 0,
      ];

      $form['additional_delivery'] = [
        '#type' => 'container',
        '#tree' => TRUE,
      ];

//    State conditon delivery_address
      $additional_delivery_condition = array(
        'visible' => array(
          ':input[name="delivery_address"]' => array('value' => '1'),
        ),
      );
      $additional_delivery_required_condition = array(
        'visible' => array(
          ':input[name="delivery_address"]' => array('value' => '1'),
        ),
        'required' => array(
          ':input[name="delivery_address"]' => array('value' => '1'),
        ),
      );

      $form['additional_delivery']['salutation'] = [
        '#type' => 'select',
        '#title' => t('Salutation'),
        '#options' => [
          1 => t('Mr.'),
          2 => t('Mrs.'),
          3 => t('Family'),
          4 => t('Mr. and Mrs.'),
          100 => t('Company'),
        ],
        '#ajax' => [
          'callback' => [get_class($this), 'validateFormAjax'],
          'event' => 'blur',
          'disable-refocus' => TRUE,
          'progress' => [
            'type' => 'throbber',
            'message' => t('validating'),
          ],
        ],
        '#element_validate' => [[get_class($this), 'validateElement']],
        '#states' => $additional_delivery_required_condition
      ];

      $form['additional_delivery']['jobTitleCode'] = [
        '#type' => 'select',
        '#title' => t('Title'),
        '#options' => [
          '' => "(optional)",
          1 => "Dr.",
          14 => "Prof.",
        ],
        '#element_validate' => [[get_class($this), 'validateElement']],
        '#states' => $additional_delivery_condition
      ];

      $form['additional_delivery']['firstName'] = [
        '#type' => 'textfield',
        '#title' => t('First name'),
        '#ajax' => [
          'callback' => [$this, "validateFormAjax"],
          'event' => 'blur',
          'disable-refocus' => TRUE,
          'progress' => [
            'type' => 'throbber',
            'message' => t('Validating'),
          ],
        ],
        '#element_validate' => [[get_class($this), 'validateElement']],
        '#states' => $additional_delivery_required_condition
      ];

      $form['additional_delivery']['surname'] = [
        '#type' => 'textfield',
        '#title' => t('Surname'),
        '#ajax' => [
          'callback' => [get_class($this), 'validateFormAjax'],
          'event' => 'blur',
          'disable-refocus' => TRUE,
          'progress' => [
            'type' => 'throbber',
            'message' => t('Validating'),
          ],
        ],
        '#element_validate' => [[get_class($this), 'validateElement']],
        '#states' => $additional_delivery_required_condition
      ];

      $form['additional_delivery']['companyName'] = [
        '#type' => 'textfield',
        '#title' => t('Company name'),
        '#states' => $additional_delivery_condition
      ];

      $form['additional_delivery']['street'] = [
        '#type' => 'textfield',
        '#title' => t('Street'),
        '#ajax' => [
          'callback' => [get_class($this), 'validateFormAjax'],
          'event' => 'blur',
          'disable-refocus' => TRUE,
          'progress' => [
            'type' => 'throbber',
            'message' => t('validating'),
          ],
        ],
        '#element_validate' => [[get_class($this), 'validateElement']],
        '#states' => $additional_delivery_required_condition,
      ];

      $form['additional_delivery']['houseNo'] = [
        '#type' => 'textfield',
        '#title' => t('House number'),
        '#ajax' => [
          'callback' => [get_class($this), 'validateFormAjax'],
          'event' => 'blur',
          'disable-refocus' => TRUE,
          'progress' => [
            'type' => 'throbber',
            'message' => t('validating'),
          ],
        ],
        '#element_validate' => [[get_class($this), 'validateElement']],
        '#states' => $additional_delivery_required_condition,
      ];

      $form['additional_delivery']['postCode'] = [
        '#type' => 'textfield',
        '#title' => t('Post code'),
        '#ajax' => [
          'callback' => [get_class($this), 'validateFormAjax'],
          'event' => 'blur',
          'disable-refocus' => TRUE,
          'progress' => [
            'type' => 'throbber',
            'message' => t('validating'),
          ],
        ],
        '#element_validate' => [[get_class($this), 'validateElement']],
        '#states' => $additional_delivery_required_condition,
      ];

      $form['additional_delivery']['city'] = [
        '#type' => 'textfield',
        '#title' => t('City'),
        '#ajax' => [
          'callback' => [get_class($this), 'validateFormAjax'],
          'event' => 'blur',
          'disable-refocus' => TRUE,
          'progress' => [
            'type' => 'throbber',
            'message' => t('validating'),
          ],
        ],
        '#element_validate' => [[get_class($this), 'validateElement']],
        '#states' => $additional_delivery_required_condition,
      ];

      $form['additional_delivery']['countryCode'] = [
        '#type' => 'select',
        '#title_display' => 'invisible',
        '#options' => WoViUserDataWidget::getAllCountryNames(),
        '#element_validate' => [[get_class($this), 'validateElement']],
        '#states' => $additional_delivery_condition,
        '#default_value' => 'DE'
      ];


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
      ->setResponseType('card')
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
