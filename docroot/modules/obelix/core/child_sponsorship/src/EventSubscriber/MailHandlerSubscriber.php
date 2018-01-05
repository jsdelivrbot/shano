<?php

namespace Drupal\child_sponsorship\EventSubscriber;

use Drupal\child\Entity\Child;
use Drupal\Core\Render\Renderer;
use Drupal\Core\Url;
use Drupal\file\Entity\File;
use Drupal\forms_suite\Event\MailHandlerEvent;
use Drupal\forms_suite\Event\MailHandlerEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


/**
 * Class MailHandlerSubscriber.
 *
 * @package Drupal\child_sponsorship
 */
class MailHandlerSubscriber implements EventSubscriberInterface
{

  /**
   * @var Renderer
   */
  private $renderer;

  /**
   * MailHandlerSubscriber constructor.
   * @param Renderer $renderer
   */
  public function __construct(Renderer $renderer)
  {
    $this->renderer = $renderer;
  }

  /**
   * Mail handler pre process params.
   *
   * @param MailHandlerEvent $event
   */
  public function mailHandlerPreProcessParams(MailHandlerEvent $event)
  {

    $data_handler = $event->getDataHandler();
    $message = $event->getMessage();
    $values = $event->getValues();
    $params = $event->getParams();
    $form = $message->getForm();

    $template = $form->getTemplateEmail();

    if ($template == 'autoreply_suggestion_email') {
      // todo that's dirty @moevoe

      $child_entity_type = \Drupal::entityTypeManager()->getStorage('child');
      $parts = explode('-', $values['field_suggestion']['childSequenceNo']);
      $children = $child_entity_type->loadByProperties([
        'field_child_project' => $parts[0],
        'field_child_childsequence' => $parts[1],
      ]);

      foreach ($children as $child) {
        /** @var Child $child */
        $file = File::load($child->get('field_child_image')
          ->getValue()[0]['target_id']);

        $url_get_suggestion_post = Url::fromUserInput('/forms/child_sponsorship_suggestion', [
          'absolute' => TRUE,
        ]);
        $url_get_child = Url::fromRoute('child_sponsorship.set_child',
          ['uuid' => $child->uuid()],
          ['absolute' => TRUE]
        );

        $data_handler->setPlaceholderValues([
          'wovi_child_select_field' => [
            'child_country' => $child->getCountry()->getName(),
            'child_name' => $child->getGivenName()->value,
            'child_age' => $child->getAge(),
            'child_lives_with' => $child->get('field_child_liveswithdesc')->value,
            'child_likes' => $child->get('field_child_playdesc')->value,
            'child_img' => file_create_url($file->getFileUri()),
            'link_get_child' => $url_get_child->toString(),
            'link_get_suggestion_post' => $url_get_suggestion_post->toString(),
          ],
        ]);
      }

      $base_template = [
        '#theme' => $template,
      ];
      $rendered_template = $this->renderer->render($base_template);
      $form->setReply($data_handler->getReply($rendered_template));
      $params['markup'] = $form->getReply();
      $params['theme'] = 'autoreply_default';
      $params['forms_message'] = $form;
      $params['subject'] = 'Persönlicher Patenschaftsvorschlag für ' . $values['field_user']['firstName'] . ' ' . $values['field_user']['surname'];

      // Send to the form recipient(s), using the site's default language.
      $params['form'] = $form;
    }

    $event->setDataHandler($data_handler);
    $event->setMessage($message);
    $event->setValues($values);
    $event->setParams($params);

  }

  /**
   * {@inheritdoc}
   */
  static function getSubscribedEvents()
  {
    if (defined('Drupal\forms_suite\Event\MailHandlerEvents::MAIL_MESSAGE_POST_PROCESS_PARAMS')) {
      $events[MailHandlerEvents::MAIL_MESSAGE_POST_PROCESS_PARAMS][] = ['mailHandlerPreProcessParams', 1000];
      return $events;
    }
  }

}
