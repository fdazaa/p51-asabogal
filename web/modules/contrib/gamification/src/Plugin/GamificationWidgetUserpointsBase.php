<?php

namespace Drupal\gamification\Plugin;

use Drupal\transaction\Entity\Transaction;

/**
 * Base for a gamification widget that makes use of userpoints.
 */
abstract class GamificationWidgetUserpointsBase extends GamificationWidgetBase {

  /**
   * Create transaction and create gamification log.
   * 
   * @param \Drupal\gamification\GamificationInterface $gamification_config_entity
   *   Gamification configuration entity.
   * @param string $method
   *   Triggering event string.
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   *   Content entity which triggered this event.
   * @param int $points
   *   Points that must be attributed to user.
   * 
   * @return bool
   *   Flag indicating if operation is executed with success.
   */
  public function executeTransaction($gamification_config_entity, $method, $entity, $points = 0) {
    $user_points = $points;
    if ($method !== 'entity_delete' && !$this->checkLimits($gamification_config_entity, $method, $entity)) {
      return FALSE;
    }

    $options = $gamification_config_entity->getWidgetOptionsValue();
    if (empty($options)) {
      return FALSE;
    }

    $uid = $this->currentUser->id();
    if ($method === 'entity_delete') {
      $result = $this->getGamificationLogByValues(['entity_id' => intval($entity->id())], ['uid', 'points', 'created']);
      if (empty($result) || !isset($result->uid) || !isset($result->points)) {
        return FALSE;
      }
      $uid = $result->uid;
      $user_points = intval($result->points);
      $user_points = $user_points * (-1);
    }
    $uid = intval($uid);
    if (empty($uid)) {
      return FALSE;
    }
    $transaction_type = empty($options['transaction_type']) ? 'userpoints_default_points' : $options['transaction_type'];
    $transaction = Transaction::create([
      'type' => $transaction_type,
    ]);
    $transaction->set('target_entity', [
      'target_id' => $uid,
      'target_type' => 'user'
    ]);
    $transaction->set('executor', $uid);
    $operation = isset($options['operation']) && !empty($options['operation']) ? $options['operation'] : '';
    if (!empty($operation)) {
      $transaction->set('operation', $operation);
    }
    $amount_field = empty($options['amount_field']) ? 'field_userpoints_default_amount' : $options['amount_field'];
    $transaction->set($amount_field, $user_points);

    $reason_value = isset($options['reason_value']) ? $options['reason_value'] : '';
    if (!empty($reason_value)) {
      $reason_field = empty($options['reason_field']) ? 'field_userpoints_default_reason' : $options['reason_field'];
      $transaction->set($reason_field, $reason_value);
    }
  
    $transaction->execute(TRUE);
    $this->createGamificationLog($gamification_config_entity, $method, $entity, $user_points);

    return TRUE;
  }

}
