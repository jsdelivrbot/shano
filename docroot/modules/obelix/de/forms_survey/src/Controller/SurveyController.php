<?php

namespace Drupal\forms_survey\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Field\FieldItemList;
use Drupal\forms\Entity\Message;

/**
 * Class SurveyController.
 *
 * @package Drupal\child\Controller
 */
class SurveyController extends ControllerBase
{
  /**
   * Statistic Page.
   *
   */
  public function statisticPage()
  {

    $message_entity_type = $this->entityTypeManager()->getStorage('forms_message');
    $all_messages = $message_entity_type->loadMultiple();


    $statistic = [];

    foreach ($all_messages as $message) {
      /** @var Message $message */
      $form = $message->getForm();
      if ($message->hasField('field_wovi_survey')) {
        /** @var FieldItemList $survey */
        $survey = $message->get('field_wovi_survey')->getValue();
        if (isset($survey[0]) && !empty($survey[0])) {
          if (!isset($statistic[$form->getOriginalId()])) {
            $statistic = [
              $form->getOriginalId() => [
                'counted_results' => [],
                'forms_submitted' => [],
              ]
            ];
          }
          if(!isset($statistic[$form->getOriginalId()]['counted_results'][$survey[0]['survey']])){
            $statistic[$form->getOriginalId()]['counted_results'] += [
              $survey[0]['survey'] => 0
            ];
          }

          $statistic[$form->getOriginalId()]['counted_results'][$survey[0]['survey']]++;
          $statistic[$form->getOriginalId()]['forms_submitted'][] = $message;
        }

      }
    }

    return [
      '#theme' => 'survey',
      '#content' => $statistic,
    ];
  }

}
