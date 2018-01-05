<?php

/**
 * @file
 * Contains \Drupal\fs_payment_payone\Controller\ExternalPaymentController.
 */

namespace Drupal\fs_payment_payone\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Drupal\forms_suite\Entity\Message;
use Drupal\forms_suite\FormInterface;
use Drupal\Core\Render\RendererInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Controller for external payment page.
 */
class ExternalPaymentController extends ControllerBase
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
   * Handle the external payment success.
   *
   * @param FormInterface $form
   * @param $uuid
   * @return mixed
   */
  public function handlePayment(FormInterface $form = NULL, $uuid)
  {

    $config = $this->config('forms.settings');

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

//
//   reference number only 20 chars
//   uuid exapmle 53f82316-7b14-41d4-8d84-06101f9f53cb = 36 chars
//   cut uuid to 18 chars example 53f82316-7b14-41d4
//   the complete uuid will be saved in the session
//   on the succesurl the session will be checked
//   case 1: session parameter is missing
//   =>
//    something went wrong with your payment.
//    It could be one of the following reasons:
//     Did you close the Browser between the payment process?
//     Did you copy the link from one Browser to another Browser?
//     Took you payment process more than 30 minutes?
//    Please contact us
//   case 2: parameter and session parameters are different
//   =>
//    something went wrong with your payment.
//    Please contact us
//   case 3: session and parameter matching.
//    process external payment

    $session_form = $this->session->get('form');
    $form_id = $form->getOriginalId();


    if (!isset($session_form[$form_id]['external'])) {
      $case = 1;
    } elseif ($session_form[$form_id]['external'] != $uuid) {
      $case = 2;
    } else {

      // set success flag in the database
      $message_entity = $this->entityTypeManager()->getStorage('forms_message');

      $messages = $message_entity->loadByProperties(['uuid' => $session_form[$form_id]['external']]);
      foreach ($messages as $message) {
        /** @var Message $message */
        $message->setExternalPayment(1);

        // check if the entity have children and set them to external payment to 1
        if(!empty($children = $message->getEntityChildren())){
          foreach ($children as $child_entity) {
            /** @var Message $child_entity */
            $child_entity->setExternalPayment(1);
            $child_entity->save();
          }
        }

        $message->save();
      }

      unset($session_form[$form_id]['external']);
      $session_form[$form_id]['success'] = TRUE;
      $session_form[$form_id]['id'] = $message->id();

      $this->session->set('form', $session_form);

      $url = Url::fromRoute('forms_suite.form.send', ['form' => $form_id]);
      return new RedirectResponse($url->toString());
    }
    return [
      '#theme' => 'external_payment',
      '#case' => $case,
    ];

  }
}
