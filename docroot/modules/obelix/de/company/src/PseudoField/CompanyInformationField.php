<?php

namespace Drupal\company\PseudoField;

/**
 * Class CompanyInformationField
 */
class CompanyInformationField extends BasePseudoField {

  /**
   * Display output for the pseudo field.
   *
   * @return array
   */
  public function output() {
    $company = $this->entity;
    $left = [];
    $right = [];

    if (!$company->field_headline->isEmpty()) {
      $left['text'] = [
        '#type' => 'html_tag',
        '#tag' => 'div',
        '#value' => $company->field_headline->value,
      ];
    }

    $left['broker_total_title'] = [
      '#type' => 'html_tag',
      '#tag' => 'div',
      '#value' => '<b>' . t('Your donations:') . '</b>',
      '#attributes' => [
        'class' => [
          'vert-offset-top-1',
        ],
      ],
    ];

    $group_count = $this->getGroupCount($company->field_motivation_code->value);
    $left['total_group'] = $group_count;

    $affiliate_count = $this->getGroupCountDoubled($company->field_motivation_code->value, $company);
    $left['total_doubled'] = $affiliate_count;

    if (!$company->field_call_to_action->isEmpty()) {
      $right['call_to_action'] = [
        '#theme' => 'editorial_button',
        '#title' => $company->field_call_to_action[0]->title,
        '#uri' => $company->field_call_to_action[0]->uri,
        '#target' => $company->field_call_to_action[0]->value,
      ];
    }

    if ($left && $right) {
      return [
        '#theme' => 'company_page_information',
        '#attributes' => [],
        '#left' => $left,
        '#right' => $right,
      ];
    }
  }

  /**
   * Get affiliate count.
   *
   * @param integer
   *   The motivation code.
   *
   * @param \Drupal\group\Entity\Group
   *   The company group.
   *
   * @return array
   *   An array to render.
   */
  private function getGroupCount($motivation_code) {
    $query = \Drupal::database()->select('message', 'm');

    $query->leftJoin('forms_message__field_giftshop', 'fg', 'fg.entity_id = m.id');

    $query->fields('m', ['id', 'affiliate_id']);
    $query->fields('fg', ['field_giftshop_amount']);

    $query->condition('m.motivation_code', $motivation_code);

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
   * Get affiliate count.
   *
   * @param integer
   *   The motivation code.
   *
   * @return array
   *   An array to render.
   */
  private function getGroupCountDoubled($motivation_code, $company) {
    $query = \Drupal::database()->select('message', 'm');

    $query->leftJoin('forms_message__field_giftshop', 'fg', 'fg.entity_id = m.id');

    $query->fields('m', ['id', 'affiliate_id']);
    $query->fields('fg', ['field_giftshop_amount']);

    $query->condition('m.motivation_code', $motivation_code);

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