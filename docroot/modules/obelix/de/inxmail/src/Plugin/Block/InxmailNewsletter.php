<?php
/**
 * @file
 * Contains \Drupal\inxmail\Plugin\Block\InxmailNewsletter.
 */
namespace Drupal\inxmail\Plugin\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormInterface;
use Drupal\forms_suite\Entity\Form;

/**
 * Provides a 'newsletter' block.
 *
 * @Block(
 *   id = "inxmail_newsletter",
 *   admin_label = @Translation("Inxmail Newsletter"),
 *   category = @Translation("Custom")
 * )
 */
class InxmailNewsletter extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $form_builder = \Drupal::service('entity.form_builder');
    $message = \Drupal::entityTypeManager()
      ->getStorage('forms_message')
      ->create([
        'form' => 'newsletter',
      ]);

    return $form_builder->getForm($message);
  }
}
