<?php

namespace Drupal\gamification\Plugin\Gamification\Widget;

use Drupal\gamification\Plugin\GamificationWidgetUserpointsBase;

/**
 * Provides a forward widget.
 *
 * @GamificationWidget(
 *   id = "forward",
 *   label = @Translation("Forward"),
 *   modules = {
 *     "forward",
 *     "userpoints",
 *   },
 *   weight = 0,
 *   entity_type = "content",
 *   source_entity_type_id = "",
 *   source_entity_bundle = {},
 *   source_events = {
 *     "forward_entity" = "forward_entity",
 *   },
 *   actions = {
 *     "forward" = @Translation("Forward"),
 *   },
 *   widget_options = {
 *     "transaction_type",
 *     "operation",
 *     "amount_field",
 *     "reason_field",
 *     "reason_value",
 *   },
 *   hide_fields = {
 *     "source.source_events"
 *   }
 * )
 */
class ForwardWidget extends GamificationWidgetUserpointsBase {

  /**
   * {@inheritdoc}
   */
  public function execute($gamification_config_entity = NULL, $method = '', $entity = NULL) {
    $points = $gamification_config_entity->getPoints();
    if (empty($points)) {
      return FALSE;
    }

    return $this->executeTransaction($gamification_config_entity, $method, $entity, intval($points));
  }

}
