<?php
/**
 * @file
 * Contains \Drupal\wvbasetheme\Plugin\Form\SearchBlockForm.
 */

namespace Drupal\wvbasetheme\Plugin\Form;

use Drupal\bootstrap\Utility\Element;
use Drupal\Core\Form\FormStateInterface;

/**
 * Implements hook_form_FORM_ID_alter().
 */
class SearchBlockForm extends \Drupal\bootstrap\Plugin\Form\SearchBlockForm
{

    /**
     * {@inheritdoc}
     */
    public function alterForm(array &$form, FormStateInterface $form_state, $form_id = NULL)
    {
        parent::alterForm($form, $form_state, $form_id);
        $container = Element::create($form, $form_state);
        $container->keys->setProperty('title', $this->t('Please enter search term…'));
//        $container->actions->submit->setProperty('icon_only', TRUE);
//        $container->keys->setProperty('input_group_button', TRUE);
    }

}
