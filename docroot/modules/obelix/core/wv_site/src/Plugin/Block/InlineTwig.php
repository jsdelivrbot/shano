<?php

namespace Drupal\wv_site\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'Contacts:left' Block.
 *
 * @Block(
 *   id = "inline_twig",
 *   admin_label = @Translation("Inline Twig"),
 *   category = @Translation("WVI"),
 * )
 */
class InlineTwig extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);

    // Retrieve existing configuration for this block.
    $config = $this->getConfiguration();

    // Add a form field to the existing block configuration form.
    $form['body'] = array(
      '#type' => 'text_format',
      '#format' => 'html',
      '#title' => t('HTML'),
      '#default_value' => isset($config['body']['value']) ? $config['body']['value'] : '',
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    // Save our custom settings when the form is submitted.
    $this->setConfigurationValue('body', $form_state->getValue('body'));
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = $this->getConfiguration();

    $active_theme = \Drupal::service('theme.manager')->getActiveTheme()->getName();
    $theme = \Drupal::service('theme_handler')->getTheme($active_theme);

    return array(
      '#type' => 'inline_template',
      '#template' => trim($config['body']['value']),
      '#context' => [
        'base_path' => base_path(),
        'directory' => $theme->subpath,
      ],
    );
  }

}
