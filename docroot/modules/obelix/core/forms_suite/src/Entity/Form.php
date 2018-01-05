<?php

/**
 * @file
 * Contains \Drupal\forms_suite\Entity\Form.
 */

namespace Drupal\forms_suite\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;
use Drupal\forms_suite\FormInterface;

/**
 * Defines the forms entity.
 *
 * @ConfigEntityType(
 *   id = "form",
 *   label = @Translation("Forms"),
 *   handlers = {
 *     "access" = "Drupal\forms_suite\FormAccessControlHandler",
 *     "list_builder" = "Drupal\forms_suite\FormListBuilder",
 *     "form" = {
 *       "add" = "Drupal\forms_suite\FormEditForm",
 *       "edit" = "Drupal\forms_suite\FormEditForm",
 *       "copy" = "Drupal\forms_suite\FormEditForm",
 *       "delete" = "Drupal\Core\Entity\EntityDeleteForm"
 *     }
 *   },
 *   config_prefix = "form",
 *   admin_permission = "administer forms",
 *   bundle_of = "forms_message",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label"
 *   },
 *   links = {
 *     "delete-form" = "/admin/structure/forms/manage/{form}/delete",
 *     "edit-form" = "/admin/structure/forms/manage/{form}",
 *     "collection" = "/admin/structure/forms",
 *     "canonical" = "/forms/{form}",
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "recipients",
 *     "reply",
 *     "weight",
 *     "template_email",
 *     "template_thank_you",
 *     "incident",
 *     "subject",
 *     "alternative_template_email",
 *     "alternative_reply",
 *     "alternative_condition_var",
 *     "alternative_condition_result",
 *     "redirect_link",
 *     "thank_you_text",
 *     "thank_you_headline",
 *     "designation_id",
 *     "motivation_code",
 *     "additional_tracking",
 *     "alias",
 *     "headline",
 *     "description",
 *     "disclaimer",
 *     "submit_button",
 *   }
 * )
 */
class Form extends ConfigEntityBundleBase implements FormInterface
{

  /**
   * The form ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The human-readable label of the category.
   *
   * @var string
   */
  protected $label;

  /**
   * List of recipient email addresses.
   *
   * @var array
   */
  protected $recipients = array();

  /**
   * An auto-reply message.
   *
   * @var string
   */
  protected $reply = '';

  /**
   * The weight of the category.
   *
   * @var int
   */
  protected $weight = 0;

  /**
   * The auto reply email template of the form.
   *
   * @var string
   */
  protected $template_email = 0;

  /**
   * The thank you template of the form.
   *
   * @var string
   */
  protected $template_thank_you = 0;

  /**
   * Incident type.
   *
   * @var string
   */
  protected $incident;

  /**
   * Subject of the auto reply.
   *
   * @var string
   */
  protected $subject;

  /**
   * Redirect link after form submit.
   *
   * @var string
   */
  protected $redirect_link;

  /**
   * Thank you page text.
   *
   * @var string
   */
  protected $thank_you_text;

  /**
   * Thank you page headline.
   *
   * @var string
   */
  protected $thank_you_headline;

  /**
   * Designation ID.
   *
   * @var string
   */
  protected $designation_id;

  /**
   * Additional tracking.
   *
   * @var string
   */
  protected $additional_tracking;

  /**
   * Motivation Code.
   *
   * @var string
   */
  protected $motivation_code;

  /**
   * Alias URL.
   *
   * @var string
   */
  protected $alias;

  /**
   * Appearance headline.
   *
   * @var string
   */
  protected $headline;

  /**
   * Appearance description.
   *
   * @var string
   */
  protected $description;

  /**
   * Appearance disclaimer.
   *
   * @var string
   */
  protected $disclaimer;

  /**
   * Appearance submit button.
   *
   * @var string
   */
  protected $submit_button;
  /**
   * Alternative e-mail template.
   *
   * @var string
   */
  protected $alternative_template_email;
  /**
   * Alternative reply text.
   *
   * @var string
   */
  protected $alternative_reply;
  /**
   * Alternative condition variable.
   *
   * @var string
   */
  protected $alternative_condition_var;
  /**
   * Alternative condition result.
   *
   * @var string
   */
  protected $alternative_condition_result;

  /**
   * {@inheritdoc}
   */
  public function getRecipients()
  {
    return $this->recipients;
  }

  /**
   * {@inheritdoc}
   */
  public function setRecipients($recipients)
  {
    $this->recipients = $recipients;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getReply()
  {
    return $this->reply;
  }

  /**
   * {@inheritdoc}
   */
  public function setReply($reply)
  {
    $this->reply = $reply;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getTemplateEmail()
  {
    return $this->template_email;
  }

  /**
   * {@inheritdoc}
   */
  public function setTemplateEmail($template_email)
  {
    $this->template_email = $template_email;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getIncident()
  {
    return $this->incident;
  }

  /**
   * {@inheritdoc}
   */
  public function setIncident($incident)
  {
    $this->incident = $incident;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getSubject()
  {
    return $this->subject;
  }

  /**
   * {@inheritdoc}
   */
  public function setSubject($subject)
  {
    $this->subject = $subject;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getRedirectLink()
  {
    return $this->redirect_link;
  }

  /**
   * {@inheritdoc}
   */
  public function setRedirectLink($redirect_link)
  {
    $this->redirect_link = $redirect_link;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getTemplateThankYou()
  {
    return $this->template_thank_you;
  }

  /**
   * {@inheritdoc}
   */
  public function setTemplateThankYou($template_thank_you)
  {
    $this->template_thank_you = $template_thank_you;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getThankYouText()
  {
    return $this->thank_you_text;
  }

  /**
   * {@inheritdoc}
   */
  public function setThankYouText($thank_you_text)
  {
    $this->thank_you_text = $thank_you_text;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getThankYouHeadline()
  {
    return $this->thank_you_headline;
  }

  /**
   * {@inheritdoc}
   */
  public function setThankYouHeadline($thank_you_headline)
  {
    $this->thank_you_headline = $thank_you_headline;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getDesignationID()
  {
    return $this->designation_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setDesignationID($designation_id)
  {
    $this->designation_id = $designation_id;
    return $this;
  }

  /**
   * @inheritDoc
   */
  public function getAdditionalTracking()
  {
    return $this->additional_tracking;
  }

  /**
   * @inheritDoc
   */
  public function setAdditionalTracking($additional_tracking)
  {
    $this->additional_tracking = $additional_tracking;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getMotivationCode()
  {
    return $this->motivation_code;
  }

  /**
   * {@inheritdoc}
   */
  public function setMotivationCode($motivation_code)
  {
    $this->motivation_code = $motivation_code;
    return $this;
  }

  /**
   * @inheritDoc
   */
  public function getAlias()
  {
    return $this->alias;
  }

  /**
   * @inheritDoc
   */
  public function setAlias($alias)
  {
    $this->alias = $alias;
    return $this;
  }

  /**
   * @inheritDoc
   */
  public function getHeadline()
  {
    return $this->headline;
  }

  /**
   * @inheritDoc
   */
  public function getDescription()
  {
    return $this->description;
  }

  /**
   * @inheritDoc
   */
  public function getDisclaimer()
  {
    return $this->disclaimer;
  }

  /**
   * @inheritDoc
   */
  public function getSubmitButton()
  {
    return $this->submit_button;
  }

  /**
   * @inheritDoc
   */
  public function setHeadline($headline)
  {
    $this->headline = $headline;
    return $this;
  }

  /**
   * @inheritDoc
   */
  public function setDescription($description)
  {
    $this->description = $description;
    return $this;
  }

  /**
   * @inheritDoc
   */
  public function setSubmitButton($submit_button)
  {
    $this->submit_button = $submit_button;
    return $this;
  }

  /**
   * @inheritDoc
   */
  public function getAlternativeTemplateEmail()
  {
    return $this->alternative_template_email;
  }

  /**
   * @inheritDoc
   */
  public function getAlternativeReply()
  {
    return $this->alternative_reply;
  }

  /**
   * @inheritDoc
   */
  public function getAlternativeConditionVar()
  {
    return $this->alternative_condition_var;
  }

  /**
   * @inheritDoc
   */
  public function getAlternativeConditionResult()
  {
    return $this->alternative_condition_result;
  }

}
