<?php

namespace Drupal\gamification\Plugin\Gamification\Widget;

use Drupal\gamification\Plugin\GamificationWidgetUserpointsBase;

/**
 * Provides a default widget.
 *
 * @GamificationWidget(
 *   id = "default_widget",
 *   label = @Translation("Default"),
 *   modules = {
 *     "userpoints",
 *   },
 *   actions = {
 *     "default" = @Translation("Default"),
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
 *     "default" = @Translation("Default"),
 *   },
 *   widget_options = {
 *     "transaction_type",
 *     "operation",
 *     "amount_field",
 *     "reason_field",
 *     "reason_value",
 *   },
 *   hide_fields = {}
 * )
 */
class DefaultWidget extends GamificationWidgetUserpointsBase {

  /**
   * {@inheritdoc}
   */
  public function execute($gamification_config_entity = NULL, $method = '', $entity = NULL) {
    $points = $gamification_config_entity->getPoints();
    if (empty($points)) {
      return FALSE;
    }

    $points = intval($points);

    return $this->executeTransaction($gamification_config_entity, $method, $entity, $points);
  }

}
