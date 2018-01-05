<?php

/**
 * @file
 * Contains Drupal\child_sponsorship\Form\ChildFinderCountry
 */

namespace Drupal\child_sponsorship\Form;

use Drupal\ab_testing\ABTestingService;
use Drupal\child\Controller\ChildController;
use Drupal\child\Entity\Child;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\InsertCommand;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Ajax\RedirectCommand;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\country\Controller\CountryController;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class ContactForm.
 *
 * @package Drupal\child_sponsorship\Form
 */
class ChildFinderCountry extends FormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return array(
      'child_sponsorship.child.finder.country.form',
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'child_sponsorship_child_finder_country_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = [];

    $form['#theme'] = 'child_sponsorship_child_country_form';
    $form['#cache']['max-age'] = 0;

    $form['gender'] = [
      '#type' => 'select',
      '#title' => t('Girl or boy'),
      '#options' => [
          0 => t('World Vision chooses'),
        ] + ChildController::getGenderOptions(),
    ];

    $form['country'] = [
      '#type' => 'select',
      '#title' => t('Country'),
      '#options' => [
          0 => t('Where it is most necessary'),
        ] + CountryController::getStaticCountryOptions(),
    ];

    $form['submit'] = array(
      '#type' => 'submit',
      '#value' => t('Find a child'),
      '#attributes' => [
        'class' => ['btn btn-beauty btn-transparent']
      ],
      '#ajax' => array(
        'callback' => array($this, 'submitFormAjax'),
        'event' => 'click',
        'progress' => array(
          'type' => 'throbber',
          'message' => t('validating'),
        ),
      ),
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

  }

  /**
   * Used when finder has only one page.
   *
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   * @param \Drupal\Core\Ajax\AjaxResponse $response
   */
  private function simpleFinderSubmit(array &$form, FormStateInterface $form_state, AjaxResponse $response) {
    $child_controller = new ChildController();

    /** @var Child $child */
    $values = $form_state->cleanValues()->getValues();
    foreach ($values as $element => $value) {
      if ($value === "0") {
        unset($values[$element]);
      }
    }

    $config = $this->config('wv_site.settings.child_finder');

    // Just show error message if finder couldn't find a child or block child for user.
    if (!($random_child = $child_controller->getRandomChild($values))
      || !$child_controller->setChildForUser($random_child)) {

      $error_message = $this->t('Could not find a child.');
      $response->addCommand(new HtmlCommand('#country-child-finder-error', $error_message));

      return;
    }

    $url = Url::fromRoute(
      $config->get('child_page_route')
      ?: 'child_sponsorship.child_sponsorship_controller_ChildSponsorshipAction'
    );

    if ($ab_testing = \Drupal::service('ab_testing')) {
      /** @var ABTestingService $ab_testing */
      $args = [
        'url' => $url,
        'values' => $form_state->cleanValues()->getValues(),
      ];
      $ab_testing->set(2, $args);
      $url = $args['url'];
    }

    $response->addCommand(new InvokeCommand(NULL, "child_finder_success", array()));
    $response->addCommand(new RedirectCommand($url->toString()));
  }

  /**
   * Used when finder has more than one page (legacy case with 3 forms).
   *
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   * @param \Drupal\Core\Ajax\AjaxResponse $response
   */
  private function finderSubmit(array &$form, FormStateInterface $form_state, AjaxResponse $response) {
    $child_controller = new ChildController();

    /** @var Child $child */
    $values = $form_state->cleanValues()->getValues();
    foreach ($values as $element => $value) {
      if ($value === "0") {
        unset($values[$element]);
      }
    }

    $config = $this->config('wv_site.settings.child_finder');
    $random_child = $child_controller->getRandomChild($values);

    if ($random_child === NULL) {

      $url = Url::fromUserInput($config->get('sponsorship_direct_uri') ?: '/forms/child_sponsorship_direct');
      $link = Link::fromTextAndUrl($this->t('click here'), $url);
      $error_message = $this->t('Could not find a child. If you are interested in sponsorships for this country @link', [
        '@link' => $link->toString()
      ]);

      // quick fix to hide the user messages and redirect directly
      $response->addCommand(new RedirectCommand($url->toString()));
      $error_message = '';

      $response->addCommand(new HtmlCommand('#country-child-finder-error', $error_message));

    }
    elseif (!$child_controller->setChildForUser($random_child)) {
      $url = Url::fromUserInput($config->get('sponsorship_suggestion_uri') ?: '/forms/child_sponsorship_suggestion');
      $link = Link::fromTextAndUrl($this->t('Let send yourself a non-binding proposal by mail.'), $url);
      $error_message = $this->t('Unfortunately we can not show your child online. @link', [
        '@link' => $link->toString()
      ]);

      // quick fix to hide the user messages and redirect directly
      $response->addCommand(new RedirectCommand($url->toString()));
      $error_message = '';

      $response->addCommand(new HtmlCommand('#country-child-finder-error', $error_message));
    }
    else {
      $url = Url::fromRoute('child_sponsorship.child_sponsorship_controller_ChildSponsorshipAction');
      if ($ab_testing = \Drupal::service('ab_testing')) {
        /** @var ABTestingService $ab_testing */
        $args = [
          'url' => $url,
          'values' => $form_state->cleanValues()->getValues(),
        ];
        $ab_testing->set(2, $args);
        $url = $args['url'];
      }
      $response->addCommand(new InvokeCommand(NULL, "child_finder_success", array()));
      $response->addCommand(new RedirectCommand($url->toString()));
    }
  }

  /**
   * @param array $form
   * @param FormStateInterface $form_state
   * @return mixed
   */
  public function submitFormAjax(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();

    $config = $this->config('wv_site.settings.child_finder');

    // Some countries don't have complex find process, so only one page with found child is enough there.
    if ($config->get('is_simple_find_process')) {
      $this->simpleFinderSubmit($form, $form_state, $response);
    }
    else {
      // On other hand countries like DE have 3 different pages depending on child find & user lock results.
      $this->finderSubmit($form, $form_state, $response);
    }

    return $response;
  }

  /**
   * {@inheritdoc}
   */
  public
  function submitForm(array &$form, FormStateInterface $form_state) {
    $form_state->setRedirect('child_sponsorship.child_sponsorship_controller_ChildSponsorshipAction');
  }

}
