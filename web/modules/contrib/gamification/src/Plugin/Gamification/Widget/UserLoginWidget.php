<?php

namespace Drupal\gamification\Plugin\Gamification\Widget;

use Drupal\gamification\Plugin\GamificationWidgetUserpointsBase;

/**
 * Provides a user login widget.
 *
 * @GamificationWidget(
 *   id = "user_login",
 *   label = @Translation("User Login"),
 *   modules = {
 *     "userpoints",
 *   },
 *   weight = 0,
 *   entity_type = "content",
 *   source_entity_type_id = "user",
 *   source_entity_bundle = {
 *     "user",
 *   },
 *   source_events = {
 *     "user_login" = "user_login",
 *   },
 *   actions = {
 *     "user_login" = @Translation("User Login"),
 *   },
 *   widget_options = {
 *     "transaction_type",
 *     "operation",
 *     "amount_field",
 *     "reason_field",
 *     "reason_value",
 *   },
 *   hide_fields = {
 *     "source",
 *   }
 * )
 */
class UserLoginWidget extends GamificationWidgetUserpointsBase {

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
