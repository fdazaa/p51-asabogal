<?php

namespace Drupal\gamification\Plugin\Gamification\Widget;

use Drupal\gamification\Plugin\GamificationWidgetUserpointsBase;

/**
 * Provides a fivestar widget.
 *
 * @GamificationWidget(
 *   id = "fivestar",
 *   label = @Translation("Fivestar"),
 *   modules = {
 *     "fivestar",
 *     "userpoints",
 *   },
 *   weight = 0,
 *   entity_type = "content",
 *   source_entity_type_id = "",
 *   source_entity_bundle = {},
 *   source_events = {
 *     "entity_insert" = "entity_insert",
 *     "entity_update" = "entity_update",
 *     "entity_delete" = "entity_delete",
 *   },
 *   actions = {
 *     "fivestar" = @Translation("Fivestar"),
 *   },
 *   widget_options = {
 *     "multiplier",
 *     "field_fivestar_machine_name",
 *     "transaction_type",
 *     "operation",
 *     "amount_field",
 *     "reason_field",
 *     "reason_value",
 *   },
 *   hide_fields = {
 *     "points",
 *   }
 * )
 */
class FivestarWidget extends GamificationWidgetUserpointsBase {

  /**
   * {@inheritdoc}
   */
  public function execute($gamification_config_entity = NULL, $method = '', $entity = NULL) {
    $options = $gamification_config_entity->getWidgetOptionsValue();
    if (!isset($options['field_fivestar_machine_name'])) {
      return FALSE;
    }

    $field_fivestar = $options['field_fivestar_machine_name'];
    $points = $entity->get($field_fivestar)->getValue()[0]['rating'];
    if (empty($points)) {
      return FALSE;
    }

    $points = (float) $points;
    $multiplier = (float) $options['multiplier'];
    
    if ($method === 'entity_delete') {
      $points = $points * (-1);
    }

    if ($method === 'entity_update') {
      $original_points = $entity->original->get($field_fivestar)->getValue()[0]['rating'];
      if ($points === $original_points) {
        return;
      } else {
        $points -= $original_points;
      }
    }
    $points = $points * $multiplier;
    return $this->executeTransaction($gamification_config_entity, $method, $entity, intval($points));
  }

}
