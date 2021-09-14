<?php

namespace Drupal\gamification\Plugin\Gamification\Widget;

use Drupal\gamification\Plugin\GamificationWidgetUserpointsBase;

/**
 * Provides a quiz widget.
 *
 * @GamificationWidget(
 *   id = "quiz",
 *   label = @Translation("Quiz"),
 *   modules = {
 *     "quiz",
 *     "userpoints", 
 *   },
 *   weight = 0,
 *   entity_type = "content",
 *   source_entity_type_id = "quiz_result",
 *   source_entity_bundle = {
 *     "quiz_result",
 *   },
 *   source_events = {
 *     "entity_update" = "entity_update",
 *     "entity_delete" = "entity_delete",
 *   },
 *   actions = {
 *     "quiz_result" = @Translation("Quiz result"),
 *   },
 *   widget_options = {
 *     "quiz_id",
 *     "transaction_type",
 *     "operation",
 *     "amount_field",
 *     "reason_field",
 *     "reason_value",
 *   },
 *   hide_fields = {
 *     "limits.entity_id",
 *     "limits.bundle",
 *     "points",
 *     "source",
 *   }
 * )
 */
class QuizWidget extends GamificationWidgetUserpointsBase {

  /**
   * {@inheritdoc}
   */
  public function execute($gamification_config_entity = NULL, $method = '', $entity = NULL) {
    if (empty($gamification_config_entity) || empty($entity)) {
      return FALSE;
    }

    $options = $gamification_config_entity->getWidgetOptionsValue();
    if (empty($options)) {
      return FALSE;
    }

    $points = intval($entity->get('score')->value);
    if (empty($points)) {
      return FALSE;
    }

    $is_evaluated = $entity->get('is_evaluated')->value;
    if (!$is_evaluated) {
      return FALSE;
    }

    if ($gamification_config_entity->getAction() !== 'quiz_result' || (!empty($options['quiz_id']) && ($options['quiz_id'] !== $entity->get('qid')->getValue()[0]['target_id']))) {
      return FALSE;
    }

    if ($method === 'entity_delete') {
      $points = $points * (-1);
    }

    return $this->executeTransaction($gamification_config_entity, $method, $entity, $points);
  }

}
