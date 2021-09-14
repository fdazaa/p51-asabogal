<?php

namespace Drupal\gamification\Plugin;

use Drupal\Component\Plugin\PluginInspectionInterface;

/**
 * Interface for the field mapping.
 */
interface GamificationWidgetInterface extends PluginInspectionInterface {

  /**
   * Check if a plugin should be visible in gamification configurations. 
   */
  public function checkAccess();

  /**
   * Action handler.
   *
   * @param \Drupal\gamification\GamificationInterface $gamification_config_entity
   *   Gamification configuration entity.
   * @param string $method
   *   Triggering event string.
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   *   Content entity which triggered this event.
   *
   * @return bool
   *   True if executed with success, otherwise return false.
   */
  public function execute($gamification_config_entity, $method, $entity);

  /**
   * Limit attribution points according to time, entity id or bundle.
   *
   * @param \Drupal\gamification\GamificationInterface $gamification_config_entity
   *   Gamification configuration entity.
   * @param string $method
   *   Triggering event string.
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   *   Content entity which triggered this event.
   *
   * @return bool
   *   True if limits are respected, otherwise return false.
   */
  public function checkLimits($gamification_config_entity, $method, $entity);

  /**
   * Get gamification log with specific conditions.
   *
   * @param array $values
   *   Values to be used in conditions.
   * @param array $fields
   *   Fields to be returned from database.
   *
   * @return mixed
   *   Result obtained from database.
   */
  public function getGamificationLogByValues($values, $fields);

  /**
   * Create gamification log after triggering some configured gamification event.
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
   *   True if created log with success, otherwise should return false.
   */
  public function createGamificationLog($gamification_config_entity, $method, $entity, $points);

}
