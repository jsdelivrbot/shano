<?php

namespace Drupal\broker_page\PseudoField;

use Drupal\group\Entity\GroupContent;

/**
 * Class CompanyInformationField
 */
class BrokerPageInformationField extends BasePseudoField {

  /**
   * Display output for the pseudo field.
   *
   * @return array
   */
  public function output() {
    $broker_page = $this->entity;
    $group_content = GroupContent::loadByEntity($broker_page);
    $group_content = reset($group_content);
    if ($group_content) {
      $company = $group_content->getGroup();
    }

    $left = [];
    $center = [];
    $right = [];

    if (!$broker_page->field_broker_image->isEmpty()) {
      $left['broker_image'] = [
        '#theme' => 'responsive_image_formatter',
        '#item' => $broker_page->field_broker_image[0],
        '#url' => NULL,
        '#responsive_image_style_id' => 'portrait_responsive',
      ];
    }

    $center['broker_title'] = [
      '#type' => 'html_tag',
      '#tag' => 'h3',
      '#value' => $broker_page->title->value,
    ];

    if ($group_content) {
      $center['broker_text'] = [
        '#type' => 'html_tag',
        '#tag' => 'div',
        '#value' => $company->field_broker_page_text->value,
      ];
    }

    if (!$broker_page->field_affiliate->isEmpty()) {
      $affiliate = reset($broker_page->field_affiliate->referencedEntities());

      $center['broker_total_title'] = [
        '#type' => 'html_tag',
        '#tag' => 'div',
        '#value' => '<b>' . t('Your donations:') . '</b>',
        '#attributes' => [
          'class' => [
            'vert-offset-top-1',
          ],
        ],
      ];

      if ($group_content) {
        $group_count = $this->getAffiliateCount($affiliate->id());
        $center['broker_total_group'] = $group_count;

        $affiliate_count = $this->getAffiliateCountDoubled($affiliate->id(), $company);
        $center['broker_total_affiliate'] = $affiliate_count;
      }
    }

    if (!$broker_page->field_broker_logo->isEmpty()) {
      $right['broker_logo'] = [
        '#theme' => 'responsive_image_formatter',
        '#item' => $broker_page->field_broker_logo[0],
        '#url' => NULL,
        '#responsive_image_style_id' => 'max_650',
      ];
    }

    return [
      '#theme' => 'broker_page_information',
      '#attributes' => [],
      '#left' => $left,
      '#center' => $center,
      '#right' => $right,
    ];
  }

  /**
   * Get affiliate count.
   *
   * @param integer
   *   The affiliate ID.
   *
   * @return array
   *   An array to render.
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

    $count = number_format((float)$count, 2, '.', ',');

    return [
      '#type' => 'html_tag',
      '#tag' => 'div',
      '#value' => t('Donated so far: @count €', ['@count' => $count]),
      '#attributes' => [
        'class' => [
          'vert-offset-top-1',
        ],
      ],
    ];
  }

  /**
   * Get affiliate count doubled.
   *
   * @param integer
   *   The affiliate ID.
   *
   * @return array
   *   An array to render.
   */
  private function getAffiliateCountDoubled($affiliate_id, $company) {
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

    $count = $count*2;
    $count = number_format((float)$count, 2, '.', ',');

    $company_name = $company->label();

    return [
      '#type' => 'html_tag',
      '#tag' => 'div',
      '#value' => t('Doubled by @company_name: @count €', ['@company_name' => $company_name, '@count' => $count]),
      '#attributes' => [
        'class' => [
          'vert-offset-top-1',
        ],
      ],
    ];
  }

}