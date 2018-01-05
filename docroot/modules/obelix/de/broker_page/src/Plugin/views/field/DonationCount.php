<?php

/**
 * @file
 * Definition of Drupal\broker_page\Plugin\views\field\DonationCount
 */

namespace Drupal\broker_page\Plugin\views\field;

use Drupal\views\ResultRow;
use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\field\FieldPluginBase;

/**
 * Field handler to display the donation count for one broker.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("donation_count")
 */
class DonationCount extends FieldPluginBase {

  /**
   * @{inheritdoc}
   */
  public function query() {
    // Leave empty to avoid a query on this field.
  }

  /**
   * Define the available options
   * @return array
   */
  protected function defineOptions() {
    $options = parent::defineOptions();
    $options['admin_label'] = array('default' => 'Donation count');
    return $options;
  }

  /**
   * Provide the options form.
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);
  }

  /**
   * @{inheritdoc}
   */
  public function render(ResultRow $values) {
    $node = $values->_entity;
    return $this->getAffiliateCount($node->field_affiliate->target_id);
  }

  /**
   * Get affiliate count.
   *
   * @param integer
   *   The affiliate ID.
   *
   * @return string
   *   The donation count.
   */
  private function getAffiliateCount($affiliate_id) {
    $query = \Drupal::database()->select('message', 'm');

    $query->leftJoin('forms_message__field_giftshop', 'fg', 'fg.entity_id = m.id');

    $query->fields('m', ['id', 'affiliate_id']);
    $query->fields('fg', ['field_giftshop_amount']);

    $query->condition('m.affiliate_id', $affiliate_id);

    $query_results = $query->execute()->fetchAll();

    $count = 0;
    foreach ($query_results as $item) {
      if ($item->field_giftshop_amount) {
        $count += $item->field_giftshop_amount;
      }
    }

    $count = number_format((float) $count, 2, '.', ',');

    return $count;
  }

}
