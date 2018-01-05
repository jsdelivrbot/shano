<?php

/**
 * @file
 * Contains \Drupal\forms_suite\Controller\FormsController.
 */

namespace Drupal\forms_suite\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Drupal\forms_suite\Event\FormSaveEvent;
use Drupal\forms_suite\Event\FormSendEvent;
use Drupal\forms_suite\Event\FormSendEvents;
use Drupal\forms_suite\FormInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\giftshop\GiftshopCartInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Controller for thank you page.
 */
class FormsSendController extends ControllerBase
{

  /**
   * The renderer.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * The session.
   *
   * @var Session
   */
  protected $session;

  /**
   * Constructs a FormsController object.
   *
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The renderer.
   * @param Session $session
   */
  public function __construct(RendererInterface $renderer, Session $session)
  {
    $this->renderer = $renderer;
    $this->session = $session;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container)
  {
    return new static(
      $container->get('renderer'),
      $container->get('session')
    );
  }

  /**
   * Presents the site-wide forms form.
   *
   * @param \Drupal\forms_suite\FormInterface $form
   *   The forms form to use.
   *
   * @return mixed
   *   The form as render array as expected by drupal_render().
   *
   * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
   *   Exception is thrown when user tries to access non existing default
   *   forms form.
   */
  public function thanKYouPage(FormInterface $form = NULL)
  {
    $config = $this->config('forms.settings');


    // event dispatcher form
    $event = new FormSendEvent($form);
    $eventDispatcher = \Drupal::service('event_dispatcher');

    // Use the default form if no form has been passed.
    if (empty($form)) {
      $form = $this->entityManager()
        ->getStorage('form')
        ->load($config->get('default_form'));
      // If there are no forms, do not display the form.
      if (empty($form)) {
        if ($this->currentUser()->hasPermission('administer forms')) {
          drupal_set_message($this->t('The forms form has not been configured. <a href=":add">Add one or more forms</a> .', array(
            ':add' => $this->url('forms.form_add'))), 'error');
          return array();
        } else {
          throw new NotFoundHttpException();
        }
      }
    }

    $session_form = $this->session->get('form');
    $form_id = $form->getOriginalId();

    if (isset($session_form[$form_id]['success']) && $session_form[$form_id]['success']) {


      // get web reference id for tracking
      $web_reference_id = $session_form[$form_id]['id'];
      // load ivision config
      $ivision_config = \Drupal::configFactory()->get('ivision.config');
      $ivision_config = $ivision_config->get('ivision');

      // Add the user given webReferenceID to the start value of the iVision webReferenceID,
      // if it is set in the config.
      if ($ivision_config !== NULL && isset($ivision_config['web_reference_id'])) {
        $web_reference_id += $ivision_config['web_reference_id'];
      }

      unset($session_form[$form_id]['success']);
      $this->session->set('form', $session_form);

      $text = $form->getThankYouText();

      // replace tokens
      $token_service = \Drupal::token();
      $text['value'] = $token_service->replace($text['value'], [
        'forms'=> [
          'transfer_id' => $web_reference_id
        ]
      ]);


      $headline = $form->getThankYouHeadline();
      $theme = $form->getTemplateThankYou();

      $result = [
        '#theme' => $theme,
        '#text' => $text['value'],
        '#headline' => $headline,
      ];

      $eventDispatcher->dispatch(FormSendEvents::FORM_SEND_SUCCESS, $event);

      return $result;

    } else {
      $url = Url::fromRoute('entity.form.canonical', ['form' => $form_id]);
      return new RedirectResponse($url->toString());
    }


  }
}
