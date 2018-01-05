<?php

/**
 * @file
 * Contains \Drupal\forms_suite\MessageForm.
 */

namespace Drupal\forms_suite;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\AppendCommand;
use Drupal\Core\Ajax\RedirectCommand;
use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element\StatusMessages;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Drupal\Core\Url;
use Drupal\forms_suite\Entity\Message;
use Drupal\forms_suite\Event\FormBaseEvent;
use Drupal\forms_suite\Event\FormEvents;
use Drupal\forms_suite\Event\FormSaveEvent;
use Drupal\forms_suite\Event\FormSubmitAjaxEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Form controller for forms message forms.
 */
class MessageForm extends ContentEntityForm
{

  /**
   * The message being used by this form.
   *
   * @var \Drupal\forms_suite\MessageInterface
   */
  protected $entity;

  /**
   * Session Manager
   *
   * @var Session
   */
  protected $session;


  protected static $one_time_form = TRUE;

  /**
   * Constructs a MessageForm object.
   *
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   *   The entity manager.
   * @param Session $session
   */
  public function __construct(EntityManagerInterface $entity_manager, Session $session)
  {
    parent::__construct($entity_manager);
    $this->session = $session;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container)
  {
    return new static(
      $container->get('entity.manager'),
      $container->get('session')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state)
  {
    $message = $this->entity;
    $form_entity = $message->getForm();

    $form = parent::form($form, $form_state, $message);
    $form['#attributes']['class'][] = 'forms-form';
    $form['#attributes']['novalidate'] = 'novalidate';

    $form['#attached']['library'][] = 'core/drupal.form';
    $form['#attributes']['data-user-info-from-browser'] = TRUE;

    $form['#theme'] = 'message_form__base';

    $form_state->setCached(FALSE);

    if (!empty($form_entity->getHeadline())) {
      $form['appearance']['headline']['#markup'] = t($form_entity->getHeadline());
    }
    if (!empty($form_entity->getDescription())) {
      $form['appearance']['description']['#markup'] = t($form_entity->getDescription());
    }
    if (!empty($form_entity->getDisclaimer())) {
      $form['appearance']['disclaimer']['#markup'] = t($form_entity->getDisclaimer());
    }

    // @todo is triggered on every ajax call. Should only triggered one time.
    $form['#attached']['library'][] = 'forms_suite/forms_suite';
    
    // event dispatcher form
    $event = new FormBaseEvent($form, $form_state);
    $eventDispatcher = \Drupal::service('event_dispatcher');
    $eventDispatcher->dispatch(FormEvents::BUILD_PROCESS, $event);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function actions(array $form, FormStateInterface $form_state)
  {
    $message = $this->entity;
    $form_entity = $message->getForm();

    $elements = parent::actions($form, $form_state);
    if ($form_entity->getSubmitButton()) {
      $elements['submit']['#value'] = $form_entity->getSubmitButton();
    } else {
      $elements['submit']['#value'] = $this->t('Send');
    }

    $elements['submit']['#ajax'] = [
      'callback' => [$this, 'submitFormAjax'],
      'event' => 'click',
      'progress' => [
        'type' => 'throbber',
        'message' => t('validating'),
      ],
    ];

    return $elements;
  }

  /**
   * @param array $form
   * @param FormStateInterface $form_state
   * @return AjaxResponse
   */
  public function submitFormAjax(array &$form, FormStateInterface $form_state)
  {
    $response = new AjaxResponse();
    $form_state->disableRedirect(FALSE);

    // event dispatcher form
    $event = new FormSubmitAjaxEvent($form, $form_state, $response);
    $eventDispatcher = \Drupal::service('event_dispatcher');

    if (empty($form_state->getErrors())) {
      // no errors
      // event dispatcher form success
      $eventDispatcher->dispatch(FormEvents::SUBMITTED_SUCCESS, $event);

      if ($form_state->getRedirect()) {
        $response->addCommand(new RedirectCommand($form_state->getRedirect()->toString()));
      } else {
        /** @var TrustedRedirectResponse $form_response */
        $form_response = $form_state->getResponse();
        $response->addCommand(new RedirectCommand($form_response->getTargetUrl()));
      }

    } else {

      $error_messages = StatusMessages::renderMessages('error');
      $response->addCommand(new AppendCommand('.main-content-wrapper .region', $error_messages));

      // event dispatcher form errors
      $eventDispatcher->dispatch(FormEvents::SUBMITTED_ERROR, $event);
    }

    return $response;
  }


  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state)
  {
    $message = parent::validateForm($form, $form_state);
    if (!empty($form_state->getErrors())) {
      drupal_get_messages('error');
    }
    return $message;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state)
  {
    /** @var Message $message */
    $message = $this->entity;
    $form_entity = $message->getForm();

    // event dispatcher form
    $event = new FormSaveEvent($form, $form_state, $message);
    $eventDispatcher = \Drupal::service('event_dispatcher');

    // to set the id
    $message->save();

    if (self::$one_time_form) {
      // bugfix to prevent a unlimited form calls.
      self::$one_time_form = FALSE;
    }

    if (!empty($form_entity->getRedirectLink())) {

      $url = Url::fromUserInput($form_entity->getRedirectLink());
      $form_state->setRedirectUrl($url);

    } else {

      $form_id = $form_entity->getOriginalId();
      $this->session->set('form', [
        $form_id => [
          'success' => TRUE,
          'id' => $message->id(),
        ]
      ]);

      $form_state->setRedirect('forms_suite.form.send', ['form' => $form_id]);
      // no external payment process
    }
    $message->setTransferID(-3);
    $eventDispatcher->dispatch(FormEvents::SUBMITTED_PRE_SAVE, $event);
    // save data and create db id.
    $message->save();

    $eventDispatcher->dispatch(FormEvents::SUBMITTED_POST_SAVE, $event);
  }
}
