<?php

namespace Drupal\company\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class CompanySubscribeForm.
 *
 * @package Drupal\company\Form
 */
class CompanySubscribeForm extends FormBase {

  /**
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entity_manager;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'company_subscribe_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $company_id = NULL) {

    $this->entity_manager = \Drupal::entityTypeManager();

    $form['#theme'] = 'company_subscribe_form';
    $form['#attributes']['class'] = ['form-minimal-border'];

    // Personal information
    $form['personal_information'] = [
      '#type' => 'fieldset',
      '#weight' => 0,
    ];

    $form['personal_information']['company_id'] = array(
      '#type' => 'hidden',
      '#value' => $company_id,
    );

    $form['personal_information']['first_name'] = array(
      '#type' => 'textfield',
      '#title' => t('First name'),
      '#required' => TRUE,
    );

    $form['personal_information']['last_name'] = array(
      '#type' => 'textfield',
      '#title' => t('Surname'),
      '#required' => TRUE,
    );

    $form['personal_information']['email'] = array(
      '#type' => 'email',
      '#title' => t('Email'),
      '#required' => TRUE,
    );

    $form['personal_information']['company_name'] = array(
      '#type' => 'textfield',
      '#title' => t('Company'),
      '#required' => FALSE,
    );

    $form['personal_information']['broker_image'] = [
      '#type' => 'managed_file',
      '#title' => t('Broker image'),
      '#upload_validators' => array(
        'file_validate_extensions' => ['gif png jpg jpeg'],
        'file_validate_size' => array(125600000),
      ),
      '#theme' => 'image_widget',
      '#preview_image_style' => 'thumbnail',
      '#upload_location' => 'public://broker_images',
      '#required' => FALSE,
    ];

    $form['personal_information']['broker_logo'] = [
      '#type' => 'managed_file',
      '#title' => t('Broker logo'),
      '#upload_validators' => array(
        'file_validate_extensions' => ['gif png jpg jpeg'],
        'file_validate_size' => array(125600000),
      ),
      '#theme' => 'image_widget',
      '#preview_image_style' => 'thumbnail',
      '#upload_location' => 'public://broker_logos',
      '#required' => FALSE,
    ];

    for($i = 0; $i < 3; $i++) {
      $form['personal_information']['gift_' . $i] = [
        '#type' => 'select',
        '#title' => $this->t('Gift #@index', ['@index' => $i+1]),
        '#description' => $this->t('Select the gift #@index', ['@index' => $i+1]),
        '#options' => $this->getCompanyGiftsOptions($company_id),
        '#required' => FALSE,
      ];
    }

    // form actions
    $form['submit'] = [
      '#type' => 'submit',
      '#title' => '',
      '#value' => t('Register as a broker for this company'),
      '#attributes' => ['class' => ['btn-beauty btn btn-xl button button--primary js-form-submit form-submit btn-primary btn']],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if ($this->checkIfUserExistByMail($form_state->getValue('email'))) {
      $form_state->setErrorByName('email', $this->t('This email address is already used by a user.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $company_group_id = $form_state->getValue('company_id');

    $account = $this->createUser($form_state);

    $this->addMemberToCompanyGroup($company_group_id, $account);

    $affiliate = $this->createAffiliate($company_group_id, $account);

    $broker_page = $this->createBrokerPage($account, $affiliate, $form_state);

    $this->addBrokerPageToCompanyGroup($company_group_id, $broker_page);

    // Send email to company owners is desactivated for now as no validation is needed for brokers.
    // $email_result = $this->sendEmailToGroupOwners($company_group_id, $account, $broker_page);

    // Send email to brokerwith information about his new page.
    $email_broker_result = $this->sendEmailToBroker($company_group_id, $account, $broker_page);

    // Give the user the "broker" role for this company as no validation is needed
    $this->addBrokerRoleForGroup($company_group_id, $account);

//    $confirmation_url = Url::fromRoute('company.subscribe_form_confirmation', ['company_id' => $company_group_id]);
//    $response = new RedirectResponse($confirmation_url->toString());
    $host = \Drupal::request()->getSchemeAndHttpHost();
    $response = new RedirectResponse($host . '/node/' . $broker_page->id());
    $response->send();
  }

  /**
   * Create user array in order to create it.
   *
   * @param \Drupal\Core\Form\FormStateInterface
   *   The form state from the form.
   *
   * @return \Drupal\core\Entity\EntityInterface
   *   The created user.
   */
  private function createUser($form_state) {
    $values = array(
      'field_firstname' => $form_state->getValue('first_name'),
      'field_surname' => $form_state->getValue('last_name'),
      'field_company' => $form_state->getValue('company_name'),
      'name' => $form_state->getValue('email'),
      'mail' => $form_state->getValue('email'),
      'roles' => array(),
      'status' => 0,
    );

    $account = $this->entity_manager->getStorage('user')->create($values);
    $account->save();

    return $account;
  }

  /**
   * Create affiliate.
   *
   * @param \Drupal\user\Entity\User
   *   The user .
   *
   * @return \Drupal\core\Entity\EntityInterface
   *   The affiliate created.
   */
  private function createAffiliate($company_group_id, $account) {
    $group = $this->entity_manager->getStorage('group')->load($company_group_id);

    $group_affiliate_group = reset($group->field_affiliate_group->referencedEntities());

    $broker_page_title = [
      $account->field_firstname->value,
      $account->field_surname->value,
    ];

    $broker_page_title = implode(' ', $broker_page_title);
    $values = array(
      'user_id' => $account->uid->value,
      'type' => 'merlin',
      'name' => $broker_page_title,
      'motivation_code' => $group->field_motivation_code->value,
      'langcode' => 'de',
      'status' => 1,
      'field_group' => [
        ['target_id' => $group_affiliate_group->id()],
      ],
    );

    $affiliate = $this->entity_manager->getStorage('affiliate')->create($values);
    $affiliate->save();

    return $affiliate;
  }

  /**
   * Create the broker page.
   *
   * @param \Drupal\entity\user
   *   The user entity.
   *
   * @return \Drupal\core\Entity\EntityInterface
   *   Broker page entity.
   */
  private function createBrokerPage($account, $affiliate, $form_state) {

    $broker_page_title = [
      $account->field_firstname->value,
      $account->field_surname->value,
      t('and World Vision help together.'),
    ];

    $broker_page_title = implode(' ', $broker_page_title);

    $field_relation_teaser_data = [];
    for ($i = 0; $i < 3; $i++) {
      if ($form_state->getValue('gift_'.$i) != 'none') {
        $field_relation_teaser_data[] = ['target_id' => $form_state->getValue('gift_'.$i)];
      }
    }

    $broker_image_data = [];
    if (empty($form_state->getValue('broker_image')) === FALSE) {
      $broker_image_data = [
        [
          'target_id' => reset($form_state->getValue('broker_image')),
          'alt' => $form_state->getValue('first_name') . ' ' . $form_state->getValue('last_name') . ' image',
        ],
      ];
    }

    $broker_logo_data = [];
    if (empty($form_state->getValue('broker_logo')) === FALSE) {
      $broker_logo_data = [
        [
          'target_id' => reset($form_state->getValue('broker_logo')),
          'alt' => $form_state->getValue('first_name') . ' ' . $form_state->getValue('last_name') . ' logo',
        ],
      ];
    }

    $values = array(
      'uid' => $account->uid->value,
      'title' => $broker_page_title,
      'type' => 'broker_page',
      'status' => 0,
      'field_affiliate' => [
        ['target_id' => $affiliate->id()],
      ],
      'field_relation_teaser' => $field_relation_teaser_data,
      'field_broker_image' => $broker_image_data,
      'field_broker_logo' => $broker_logo_data,
    );

    $broker_page = $this->entity_manager->getStorage('node')->create($values);
    $broker_page->save();

    return $broker_page;
  }

  /**
   * Add member to company group.
   *
   * @param integer
   *   The company group ID.
   * @param \Drupal\entity\user
   *   The user entity.
   */
  private function addMemberToCompanyGroup($company_group_id, $account) {
    $group = $this->entity_manager->getStorage('group')->load($company_group_id);
    $group->addMember($account);
  }

  /**
   * Add "broker" role for this group.
   *
   * @param integer
   *   The company group ID.
   * @param \Drupal\entity\user
   *   The user entity.
   */
  private function addBrokerRoleForGroup($company_group_id, $account) {
    $group = $this->entity_manager->getStorage('group')->load($company_group_id);
    $membership = $group->getMember($account);
    $roles = $membership->getRoles();
    if (!isset($roles['company-broker'])) {
      $group_content = $membership->getGroupContent();
      $group_content->group_roles->target_id = 'company-broker';
      $group_content->save();
    }
  }

  /**
   * Add broker page to company group.
   *
   * @param integer
   *   The company group ID.
   * @param \Drupal\node\Entity\Node
   *   The node entity.
   */
  private function addBrokerPageToCompanyGroup($company_group_id, $broker_page) {
    $group = $this->entity_manager->getStorage('group')->load($company_group_id);
    $group->addContent($broker_page, 'group_node:broker_page');
  }

  /**
   * Send email to group owners.
   *
   * @param integer
   *   The company group ID.
   * @param \Drupal\entity\user
   *   The user entity.
   *
   * @return integer
   *   The results of the send mail action.
   */
//  private function sendEmailToGroupOwners($company_group_id, $account, $broker_page) {
//
//    $group = $this->entity_manager->getStorage('group')->load($company_group_id);
//
//    $owners = $group->getMembers(['company-owner']);
//    $owners_to = [];
//    foreach ($owners as $membership) {
//      $user = $membership->getUser();
//      $owners_to[] = $user->mail->value;
//    }
//    $owners_to = implode(', ', $owners_to);
//
//    $broker_name = [
//      $account->field_firstname->value,
//      $account->field_surname->value,
//      '('.$account->field_company->value.')',
//    ];
//    $broker_name = implode(' ', $broker_name);
//
//    $mailManager = \Drupal::service('plugin.manager.mail');
//    $module = 'company';
//    $key = 'company_broker_subscribe';
//    $to = $owners_to;
//    $url = Url::fromUri('internal:/group/' . $company_group_id . '/members');
//    $group_member_page_link = \Drupal::l('Members page', $url);
//    $message = [];
//    $message[] = t('A new broker requested to be a member of your company page.');
//    $message[] = t('To verify this broker, edit this member and add the "broker" role from this page:');
//    $message[] = $group_member_page_link->getGeneratedLink();
//    $params['message'] = check_markup(implode('<br>', $message), 'html');
//    $params['broker_name'] = $broker_name;
//    $langcode = 'de';
//    $send = true;
//    $result = $mailManager->mail($module, $key, $to, $langcode, $params, NULL, $send);
//
//    return $result;
//  }

  /**
   * Send email to broker.
   *
   * @param integer
   *   The company group ID.
   * @param \Drupal\entity\user
   *   The user entity.
   *
   * @return integer
   *   The results of the send mail action.
   */
  private function sendEmailToBroker($company_group_id, $account, $broker_page) {

    $mailManager = \Drupal::service('plugin.manager.mail');
    $module = 'company';
    $key = 'message_broker_created_confirmation';
    $to = $account->mail->value;
    $broker_page_url = Url::fromUri('internal:/node/' . $broker_page->id(), ['absolute' => TRUE]);
    $broker_page_link = \Drupal::l('Your broker page', $broker_page_url);
    $user_login_url = Url::fromUri('internal:/user/login', ['absolute' => TRUE]);
    $user_login_link = \Drupal::l('Login page link', $user_login_url);
    $user_pass_reset_url = user_pass_reset_url($account, ['absolute' => TRUE]);
    $message = [];
    $message[] = t('Your broker page "%title" has been created.', ['%title' => $broker_page->title->value]);
    $message[] = t('You can view it here:');
    $message[] = ' ';
    $message[] = $broker_page_link->getGeneratedLink();
    $message[] = ' ';
    $message[] = t('If this is the first time you sign in on World Vision website, please click on this link (or copy it in your browser) and then change your password:');
    $message[] = ' ';
    $message[] = '<a href="' . $user_pass_reset_url . '">' . $user_pass_reset_url . '</a>';
    $message[] = ' ';
    $message[] = t('In order to edit your broker page, please sign in:');
    $message[] = ' ';
    $message[] = $user_login_link->getGeneratedLink();
    $message[] = t('Username:') . ' ' . $account->name->value;
    $message[] = t('Password:') . ' Your password';
    $message[] = ' ';
    $message[] = t('--  World Vision team');
    $params['message'] = check_markup(implode('<br>', $message), 'html');
    $params['broker_page_title'] = $broker_page->title->value;
    $langcode = 'de';
    $send = true;

    $result = $mailManager->mail($module, $key, $to, $langcode, $params, NULL, $send);

    return $result;
  }

  /**
   * Get company gifts options.
   *
   * @param integer
   *   The company id.
   *
   * @return array
   *   The gifts options.
   */
  private function getCompanyGiftsOptions($company_id) {
    $group = $this->entity_manager->getStorage('group')->load($company_id);
    $giftsOptions = ['none' => t('Select a gift')];
    foreach ($group->field_relation_teaser->referencedEntities() as $gift) {
      $giftsOptions[$gift->id()] = $gift->label();
    }
    return $giftsOptions;
  }

  /**
   * Check if user exist by mail.
   *
   * @param string
   *   The user email.
   *
   * @return boolean
   *   The user exists.
   */
  private function checkIfUserExistByMail($mail) {
    $query = \Drupal::database()->select('users_field_data', 'u');
    $query->fields('u', ['uid']);
    $query->condition('u.mail', $mail);

    $query_results = $query->execute()->fetchAll();
    if (!empty($query_results)) {
      return TRUE;
    }

    return FALSE;
  }

}