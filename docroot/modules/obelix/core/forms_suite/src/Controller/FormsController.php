<?php

/**
 * @file
 * Contains \Drupal\forms_suite\Controller\FormsController.
 */

namespace Drupal\forms_suite\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\forms_suite\FormInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\user\UserInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Controller routines for forms routes.
 */
class FormsController extends ControllerBase {

  /**
   * The renderer.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * Constructs a FormsController object.
   *
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The renderer.
   */
  public function __construct(RendererInterface $renderer) {
    $this->renderer = $renderer;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('renderer')
    );
  }

  /**
   * Presents the site-wide forms form.
   *
   * @param \Drupal\forms_suite\FormInterface $form
   *   The forms form to use.
   *
   * @return array
   *   The form as render array as expected by drupal_render().
   *
   * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
   *   Exception is thrown when user tries to access non existing default
   *   forms form.
   */
  public function formsSitePage(FormInterface $form = NULL) {
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
        }
        else {
          throw new NotFoundHttpException();
        }
      }
    }

    $message = $this->entityManager()
      ->getStorage('forms_message')
      ->create(array(
        'form' => $form->id(),
      ));

    $form_build = $this->entityFormBuilder()->getForm($message);
    $form_build['#title'] = $form->label();
    $form_build['#cache']['contexts'][] = 'user.permissions';
    $this->renderer->addCacheableDependency($form_build, $config);
    return $form_build;
  }
}
