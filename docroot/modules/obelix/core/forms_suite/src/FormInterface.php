<?php

/**
 * @file
 * Contains \Drupal\forms_suite\FormInterface.
 */

namespace Drupal\forms_suite;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface defining a forms form entity.
 */
interface FormInterface extends ConfigEntityInterface {

  /**
   * Returns list of recipient email addresses.
   *
   * @return array
   *   List of recipient email addresses.
   */
  public function getRecipients();

  /**
   * Returns an auto-reply message to send to the message author.
   *
   * @return string
   *  An auto-reply message
   */
  public function getReply();

  /**
   * Sets list of recipient email addresses.
   *
   * @param array $recipients
   *   The desired list of email addresses of this category.
   *
   * @return $this
   */
  public function setRecipients($recipients);

  /**
   * Sets an auto-reply message to send to the message author.
   *
   * @param string $reply
   *   The desired reply.
   *
   * @return $this
   */
  public function setReply($reply);

  /**
   * Return the auto reply email template.
   *
   * @return string
   */
  public function getTemplateEmail();

  /**
   * Set the auto reply email template.
   *
   * @param $template_email
   * @return $this
   */
  public function setTemplateEmail($template_email);

  /**
   * Return the incident type.
   *
   * @return string
   */
  public function getIncident();

  /**
   * Set the incident type.
   *
   * @param int $template
   * @return $this
   */
  public function setIncident($template);

  /**
   * Return the subject of the auto reply mail.
   *
   * @return string
   */
  public function getSubject();

  /**
   * Set the subject of the auto reply.
   *
   * @param int $template
   * @return $this
   */
  public function setSubject($template);

  /**
   * Return the redirect link.
   *
   * @return string
   */
  public function getRedirectLink();

  /**
   * Set the redirect link.
   *
   * @param $redirect_link
   * @return $this
   */
  public function setRedirectLink($redirect_link);

  /**
   * Return the thank you page template.
   *
   * @return string
   */
  public function getTemplateThankYou();

  /**
   * Set the auto reply email template.
   *
   * @param $template_thank_you
   * @return $this
   */
  public function setTemplateThankYou($template_thank_you);

  /**
   * Return the thank you text.
   *
   * @return string
   */
  public function getThankYouText();

  /**
   * Set the thank you text.
   *
   * @param $thank_you_text
   * @return $this
   */
  public function setThankYouText($thank_you_text);

  /**
   * Return the thank you headline.
   *
   * @return string
   */
  public function getThankYouHeadline();

  /**
   * Set the thank you headline.
   *
   * @param $thank_you_headline
   * @return $this
   */
  public function setThankYouHeadline($thank_you_headline);

  /**
   * Return the designation ID.
   *
   * @return string
   */
  public function getDesignationID();

  /**
   * Set the designation ID.
   *
   * @param $designation_id
   * @return $this
   */
  public function setDesignationID($designation_id);

  /**
   * Return the Additional tracking.
   *
   * @return string
   */
  public function getAdditionalTracking();

  /**
   * Set the Additional tracking.
   *
   * @param $additional_tracking
   * @return $this
   */
  public function setAdditionalTracking($additional_tracking);

  /**
   * Return the motivation code.
   *
   * @return string
   */
  public function getMotivationCode();

  /**
   * Set the motivation code.
   *
   * @param $motivation_code
   * @return $this
   */
  public function setMotivationCode($motivation_code);

  /**
   * Return the URL alias.
   *
   * @return string
   */
  public function getAlias();

  /**
   * Set the URL alias.
   *
   * @param $alias
   * @return $this
   */
  public function setAlias($alias);

  /**
   * Return the appearance headline.
   *
   * @return string
   */
  public function getHeadline();

  /**
   * Return the appearance description.
   *
   * @return string
   */
  public function getDescription();

  /**
   * Return the appearance disclaimer.
   *
   * @return string
   */
  public function getDisclaimer();

  /**
   * Return the appearance submit button.
   *
   * @return string
   */
  public function getSubmitButton();

  /**
   * Set the appearance headline.
   *
   * @param $appearance_headline
   * @return $this
   */
  public function setHeadline($appearance_headline);

  /**
   * Set the appearance description.
   *
   * @param $appearance_description
   * @return $this
   */
  public function setDescription($appearance_description);

  /**
   * Set the appearance submit button.
   *
   * @param $appearance_submit_button
   * @return $this
   */
  public function setSubmitButton($appearance_submit_button);

  /**
   * Return the alternative e-mail template.
   *
   * @return string
   */
  public function getAlternativeTemplateEmail();
  /**
   * Return the alternative reply text.
   *
   * @return string
   */
  public function getAlternativeReply();
  /**
   * Return the alternative condition variable.
   *
   * @return string
   */
  public function getAlternativeConditionVar();

  /**
   * Return the alternative condition result.
   *
   * @return string
   */
  public function getAlternativeConditionResult();

}
