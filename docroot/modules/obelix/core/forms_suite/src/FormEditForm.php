<?php

/**
 * @file
 * Contains \Drupal\forms_suite\FormEditForm.
 */

namespace Drupal\forms_suite;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityFieldManager;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\forms_suite\Entity\Form;
use Drupal\forms_suite\Event\FormEditEvent;
use Drupal\forms_suite\Event\FormEditEvents;
use Drupal\replicate\Replicator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Form\ConfigFormBaseTrait;
use Drupal\Core\Form\FormStateInterface;
use Egulias\EmailValidator\EmailValidator;


/**
 * Base form for forms edit form.
 */
class FormEditForm extends EntityForm implements ContainerInjectionInterface
{
  use ConfigFormBaseTrait;

  /**
   * The email validator.
   *
   * @var \Egulias\EmailValidator\EmailValidator
   */
  protected $emailValidator;

  /**
   * Constructs a new FormEditForm.
   *
   * @param \Egulias\EmailValidator\EmailValidator $email_validator
   *   The email validator.
   */
  public function __construct(EmailValidator $email_validator)
  {
    $this->emailValidator = $email_validator;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container)
  {
    return new static(
      $container->get('email.validator')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames()
  {
    return ['forms.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state)
  {
    $form = parent::form($form, $form_state);
    /** @var Form $form_entity */


    // check if uuid is set as parameter. In this case the entity should be copied.
    if ($request_uuid = \Drupal::request()->attributes->get('uuid')) {
      // load the source entity by uuid.
      $forms_entity_type = $this->entityTypeManager->getStorage('form');
      $entity_copy_sources = $forms_entity_type->loadByProperties(['uuid' => $request_uuid]);
      foreach ($entity_copy_sources as $entity) {
        /** @var Form $entity_copy_source */
        $entity_copy_source = $entity;
      }

      // clone the entity and delete the label.
      if (!empty($entity_copy_source)) {
        /** @var Replicator $replicator */
        $replicator = \Drupal::service('replicate.replicator');
        $form_entity = $replicator->cloneEntity($entity_copy_source);
        $form_entity->set('label', '');
      }
    } else {
      $form_entity = $this->entity;
    }


    $form['label'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $form_entity->label(),
      '#description' => $this->t("Example: 'website feedback' or 'product information'."),
      '#required' => TRUE,
      '#weight' => 0,
    );
    $form['id'] = array(
      '#type' => 'machine_name',
      '#default_value' => $form_entity->id(),
      '#maxlength' => EntityTypeInterface::BUNDLE_MAX_LENGTH,
      '#machine_name' => array(
        'exists' => '\Drupal\forms_suite\Entity\Form::load',
      ),
      '#disabled' => !$form_entity->isNew(),
      '#weight' => 10,
    );

    $form['alias'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('URL alias'),
      '#default_value' => $form_entity->getAlias(),
      '#description' => $this->t("URL alias for the form. Example /forms/child-sponsorship/child-suggestion"),
      '#weight' => 20,
    );

    $form['recipients'] = array(
      '#type' => 'textarea',
      '#title' => $this->t('Recipients'),
      '#default_value' => (empty($form_entity->getRecipients())) ? '' : implode(', ', $form_entity->getRecipients()),
      '#description' => $this->t("Example: 'webmaster@example.com' or 'sales@example.com,support@example.com' . To specify multiple recipients, separate each email address with a comma."),
      '#weight' => 30,
    );

    $form['autoreply'] = array(
      '#type' => 'details',
      '#title' => $this->t('Auto reply email'),
      '#description' => $this->t('Configuration of your auto reply mail. Leave empty if you dont\'t need a auto reply mail'),
      '#weight' => 50,
    );

    $form['autoreply']['subject'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Subject'),
      '#default_value' => $form_entity->getSubject(),
      '#description' => $this->t("Enter the subject of your autoreply email"),
    );


    $options = [
      '0' => 'plain text'
    ];

    $email_templates = file_scan_directory(drupal_get_path('module', 'forms_suite') . '/templates/emails/', '/html.twig$/');
    foreach ($email_templates as $email_template) {
      $template_prefix = substr($email_template->name, 0, -5);
      $template_prefix_register = str_replace('-', '_', $template_prefix);
      $options[$template_prefix_register] = $template_prefix;
    }
    ksort($options);

    $form['autoreply']['template_email'] = array(
      '#type' => 'select',
      '#title' => $this->t('E-Mail Template'),
      '#default_value' => $form_entity->getTemplateEmail(),
      '#options' => $options,
      '#description' => $this->t('Select an email template for the auto-reply mail'),
    );

    $form['autoreply']['reply'] = array(
      '#type' => 'textarea',
      '#title' => $this->t('Auto reply message'),
      '#default_value' => $form_entity->getReply(),
      '#description' => $this->t('Auto reply message. Set Tokens like [user_data:email]'),
    );

    $form['autoreply']['tokens'] = array(
      '#theme' => 'token_tree_link',
      '#token_types' => [],
    );

    $form['autoreply']['alternative'] = array(
      '#type' => 'details',
      '#title' => $this->t('Alternative auto reply email'),
      '#description' => $this->t('Set a condition to send out the alternative auto reply e-mail.'),
    );

    $form['autoreply']['alternative']['condition'] = [
      '#type' => 'details',
      '#title' => $this->t('Condition.'),
      '#open' => TRUE,
    ];

    $form['autoreply']['alternative']['condition']['alternative_condition_var'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('The variable to proof.'),
      '#default_value' => $form_entity->getAlternativeConditionVar(),
      '#description' => $this->t('Alternative auto reply condition variable. Set Tokens like [user_data:email]'),
    );
    $form['autoreply']['alternative']['condition']['alternative_condition_result'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('The variable result to proof.'),
      '#default_value' => $form_entity->getAlternativeConditionResult(),
      '#description' => $this->t('Alternative auto reply condition result.'),
    );

    $form['autoreply']['alternative']['alternative_template_email'] = array(
      '#type' => 'select',
      '#title' => $this->t('Alternative E-Mail Template'),
      '#default_value' => $form_entity->getAlternativeTemplateEmail(),
      '#options' => $options,
      '#description' => $this->t('Select an email template for the alternative auto-reply mail'),
    );

    $form['autoreply']['alternative']['alternative_reply'] = array(
      '#type' => 'textarea',
      '#title' => $this->t('Alternative auto reply message'),
      '#default_value' => $form_entity->getAlternativeReply(),
      '#description' => $this->t('Alternative auto reply message. Set Tokens like [user_data:email]'),
    );

    $form['autoreply']['alternative']['tokens'] = array(
      '#theme' => 'token_tree_link',
      '#token_types' => [],
    );


    $form['thank_you_page'] = [
      '#type' => 'details',
      '#title' => $this->t('Thank you page'),
      '#open' => FALSE,
      '#description' => $this->t('Enter a redirect link or a text for the thank you page. If you ignore both user will be redirected to default thank you page'),
      '#weight' => 60,
    ];

    // @todo use the logic from "menu add link field"
    $form['thank_you_page']['redirect_link'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Redirect link'),
      '#default_value' => $form_entity->getRedirectLink(),
      '#maxlength' => 1024,
      '#description' => $this->t('Enter internal link like /node/add or external link like https://www.example.com.'),
    );

    $options = [];
    $thankyou_templates = file_scan_directory(drupal_get_path('module', 'forms_suite') . '/templates/thank_you/', '/html.twig$/');
    foreach ($thankyou_templates as $thankyou_template) {
      $template_prefix = substr($thankyou_template->name, 0, -5);
      $template_prefix_register = str_replace('-', '_', $template_prefix);
      $options[$template_prefix_register] = $template_prefix;
    }
    ksort($options);

    $form['thank_you_page']['template_thank_you'] = array(
      '#type' => 'select',
      '#title' => $this->t('Thank you page template'),
      '#default_value' => $form_entity->getTemplateThankYou(),
      '#options' => $options,
      '#description' => $this->t('Select an thank you page template.'),
    );

    $form['thank_you_page']['thank_you_headline'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Thank you headline'),
      '#default_value' => $form_entity->getThankYouHeadline(),
    );

    $form['thank_you_page']['thank_you_text'] = array(
      '#type' => 'text_format',
      '#title' => $this->t('Thank you text'),
      '#default_value' => $form_entity->getThankYouText()['value'],
    );

    $form['tracking'] = [
      '#type' => 'details',
      '#title' => $this->t('Tracking'),
      '#open' => FALSE,
      '#description' => $this->t('Set tracking parameters for the form'),
      '#weight' => 70,
    ];

    $form['tracking']['motivation_code'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Motivation Code'),
      '#default_value' => $form_entity->getMotivationCode(),
    );

    $form['tracking']['designation_id'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Designation ID'),
      '#default_value' => $form_entity->getDesignationID(),
    );

    $form['tracking']['additional_tracking'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Additional tracking'),
      '#default_value' => $form_entity->getAdditionalTracking(),
    );

    $form['appearance'] = [
      '#type' => 'details',
      '#title' => $this->t('Appearance'),
      '#open' => FALSE,
      '#description' => $this->t('Customize the appearance of your form'),
      '#weight' => 80,
    ];

    $form['appearance']['headline'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Form headline'),
      '#default_value' => $form_entity->getHeadline()
    ];

    $form['appearance']['description'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Form description text'),
      '#default_value' => $form_entity->getDescription(),
    ];

    $form['appearance']['disclaimer'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Form disclaimer text'),
      '#default_value' => $form_entity->getDisclaimer(),
    ];

    $form['appearance']['submit_button'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Form submit button'),
      '#default_value' => $form_entity->getSubmitButton(),
    ];


    // event dispatcher
    $event = new FormEditEvent($form, $form_state, $form_entity);
    $eventDispatcher = \Drupal::service('event_dispatcher');
    $eventDispatcher->dispatch(FormEditEvents::FORM_EDIT_BUILD, $event);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state)
  {
    parent::validateForm($form, $form_state);

    if (!empty($form_state->getValue('alias'))) {
      if (substr($form_state->getValue('alias'), 0, 1) != '/') {
        $form_state->setErrorByName('alias', $this->t('URL alias %alias should start with a /', array('%alias' => $form_state->getValue('alias'))));
      }
    }

    $recipients = explode(',', $form_state->getValue('recipients'));
    if (!empty($recipients[0])) {
      // Validate and each email recipient.

      foreach ($recipients as &$recipient) {
        $recipient = trim($recipient);
        if (!$this->emailValidator->isValid($recipient)) {
          $form_state->setErrorByName('recipients', $this->t('%recipient is an invalid email address.', array('%recipient' => $recipient)));
        }
      }
      $form_state->setValue('recipients', $recipients);
    }

    /** @var FormInterface $form_entity */
    $form_entity = $this->entity;

    // event dispatcher
    $event = new FormEditEvent($form, $form_state, $form_entity);
    $eventDispatcher = \Drupal::service('event_dispatcher');
    $eventDispatcher->dispatch(FormEditEvents::FORM_EDIT_VALIDATE, $event);
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state)
  {

    /** @var FormInterface $form_entity */
    $form_entity = $this->entity;

    // set the alias if the alias textfield is filled and always delete the old alias.
    $form_path = '/forms/' . $form_entity->getOriginalId();
    \Drupal::service('path.alias_storage')->delete(['source' => $form_path]);
    if (!empty($form_entity->getAlias())) {
      \Drupal::service('path.alias_storage')->save('/forms/' . $form_entity->getOriginalId(), $form_entity->getAlias());
    }

    $status = $form_entity->save();

    if ($status == SAVED_UPDATED) {
      drupal_set_message($this->t('Form %label has been updated.', array('%label' => $form_entity->label())));
    } else {
      drupal_set_message($this->t('Form %label has been added.', array('%label' => $form_entity->label())));
    }

    // check if uuid is set as parameter. In this case the entity should be copied.
    if ($request_uuid = \Drupal::request()->attributes->get('uuid')) {
      // load the source entity by uuid.
      $forms_entity_type = $this->entityTypeManager->getStorage('form');
      $entity_original_sources = $forms_entity_type->loadByProperties(['uuid' => $request_uuid]);
      foreach ($entity_original_sources as $entity) {
        /** @var Form $entity_original_sources */
        $entity_original_source = $entity;
      }

      // get list of all fields connected to the source entity.
      /** @var EntityFieldManager $field_manager */
      $field_manager = \Drupal::service('entity_field.manager');
      $field_map = $field_manager->getFieldMap()['forms_message'];
      $used_field_list = [];
      foreach ($field_map as $fieldname => $fieldmapping) {
        if (strpos($fieldname, 'field_') > -1) {
          foreach ($fieldmapping['bundles'] as $bundle) {
            if ($bundle == $entity_original_source->get('id')) {
              $used_field_list[$fieldmapping['type']] = $fieldname;
            }
          }
        }
      }

      // add the same fields to the new entity.
      foreach ($used_field_list as $field_id => $used_field) {
        $field_storage = FieldStorageConfig::loadByName('forms_message', $used_field);
        $field = FieldConfig::loadByName('forms_message', $form_entity->get('id'), $used_field);
        $field_original = FieldConfig::loadByName('forms_message', $entity_original_source->get('id'), $used_field);

        if (empty($field)) {
          $field = FieldConfig::create([
            'field_storage' => $field_storage,
            'bundle' => $form_entity->get('id'),
            'settings' => [
              'headline' => $field_original->getSetting('headline'),
              'subline' => $field_original->getSetting('subline'),
            ],
          ]);
          $field->save();

          // @todo change deprecated functions
          // Assign widget settings for the 'default' form mode.
          entity_get_form_display('forms_message', $form_entity->get('id'), 'default')
            ->setComponent($used_field, array(
              'type' => str_replace('_field', '_widget', $field_id),
            ))
            ->save();

          // Assign display settings for the 'default' view modes.
          entity_get_display('forms_message', $form_entity->get('id'), 'default')
            ->setComponent($used_field, array(
              'label' => 'above',
              'type' => 'wovi_formatter',
            ))
            ->save();
        }
      }
    }
    $form_state->setRedirect('entity.form.collection');

    // event dispatcher
    $event = new FormEditEvent($form, $form_state, $form_entity);
    $eventDispatcher = \Drupal::service('event_dispatcher');
    $eventDispatcher->dispatch(FormEditEvents::FORM_EDIT_SAVE, $event);
  }

}
