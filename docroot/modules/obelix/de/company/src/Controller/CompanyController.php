<?php

namespace Drupal\company\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Form\FormBuilderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class CompanyController.
 *
 * @package Drupal\company\Controller
 */
class CompanyController extends ControllerBase {

  protected $entity_manager;

  /**
   * The form builder.
   *
   * @var \Drupal\Core\Form\FormBuilderInterface
   */
  protected $formBuilder;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('form_builder')
    );
  }

  public function __construct(FormBuilderInterface $form_builder) {
    $this->formBuilder = $form_builder;
    $this->entity_manager = \Drupal::entityTypeManager();
  }

  /**
   * Build user report form.
   *
   * @param integer $company_id
   *   The company ID the user subscribed to.
   *
   * @return array
   *   An array ready for drupal_render().
   */
  public function buildCompanySubscribeForm($company_id) {
    $form = $this->formBuilder->getForm('Drupal\company\Form\CompanySubscribeForm', $company_id);
    $company = $this->entity_manager->getStorage('group')->load($company_id);
    $header = [];
    $introduction = [];

    if (!$company->field_campaign_teaser->isEmpty()) {
      $view_builder = \Drupal::entityTypeManager()->getViewBuilder('editorial_content');
      $header = render($view_builder->view($company->field_campaign_teaser->referencedEntities()[0], 'full'));
    }

    if (!$company->field_company_registration_text->isEmpty()) {
      $introduction = $company->field_company_registration_text->value;
    }

    return [
      '#theme' => 'company_subscribe_form_page',
      '#form' => $form,
      '#introduction' => $introduction,
      '#header' => $header,
      '#cache' => [
        'max-age' => 0,
      ],
    ];
  }

  /**
   * Build the subscription confirmation page.
   *
   * @param integer $company_id
   *   The company ID the user subscribed to.
   *
   * @return array
   *   An array ready for drupal_render().
   */
  public function subscribeConfirmation($company_id){
    $company = $this->entity_manager->getStorage('group')->load($company_id);
    $header = [];

    $message = $company->field_registration_confirm_text->value;

    if (!$company->field_campaign_teaser->isEmpty()) {
      $view_builder = \Drupal::entityTypeManager()->getViewBuilder('editorial_content');
      $header = render($view_builder->view($company->field_campaign_teaser->referencedEntities()[0], 'full'));
    }

    return [
      '#theme' => 'company_subscribe_form_confirmation',
      '#message' => $message,
      '#header' => $header,
      '#cache' => [
        'max-age' => 0,
      ],
    ];
  }

}