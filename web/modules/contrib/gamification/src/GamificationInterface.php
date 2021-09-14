<?php

namespace Drupal\gamification;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface defining Gamification entity.
 */
interface GamificationInterface extends ConfigEntityInterface {
  const gamification = 'gamification';

  /**
   * Returns the entity title.
   *
   * @return string
   *   The entity title.
   */
  public function getTitle();

  /**
   * Sets the entity title.
   *
   * @param string $title
   *   Title.
   *
   * @return $this
   *   The Gamification entity.
   */
  public function setTitle($title);

  /**
   * Returns the gamification widget id.
   *
   * @return string
   *   The widget id.
   */
  public function getWidgetValue();

  /**
   * Sets the widget title.
   *
   * @param string $widget
   *   Gamification widget title.
   *
   * @return $this
   *   The Gamification entity.
   */
  public function setWidgetValue($widget);

  /**
   * Returns the gamification widget action.
   *
   * @return string
   *   The widget action.
   */
  public function getAction();

  /**
   * Sets the widget action.
   *
   * @param string $action
   *   Gamification widget action.
   *
   * @return $this
   *   The Gamification entity.
   */
  public function setAction($action);

  /**
   * Returns the entity points.
   *
   * @return string
   *   The entity points.
   */
  public function getPoints();

  /**
   * Sets the entity points.
   *
   * @param string $points
   *   Points.
   *
   * @return $this
   *   The Gamification entity.
   */
  public function setPoints($points);

  /**
   * Returns the widget options values.
   *
   * @return array
   *   The widget options value.
   */
  public function getWidgetOptionsValue();

  /**
   * Set the widget options values.
   *
   * @param array $attributes
   *   Widget options value.
   *
   * @return $this
   *   The Gamification entity.
   */
  public function setWidgetOptionsValue($attributes);

  /**
   * Returns the widget options keys.
   *
   * @return array
   *   The widget options keys.
   */
  public function getWidgetOptions();

  /**
   * Returns the source entity type.
   *
   * @return string
   *   The source entity type id.
   */
  public function getSourceEntityTypeId();

  /**
   * Set the source entity type.
   *
   * @param string $input
   *   Input.
   * 
   * @return $this
   *   The Gamification entity.
   */
  public function setSourceEntityId($input);

  /**
   * Returns the source bundle.
   *
   * @return string
   *   The source entity bundle.
   */
  public function getSourceBundle();

  /**
   * Set the source bundle.
   *
   * @param string $input
   *   Input.
   *
   * @return $this
   *   The Gamification entity.
   */
  public function setSourceBundle($input);

  /**
   * Returns the source triggering source events/methods.
   *
   * @return string
   *   The source entity bundle.
   */
  public function getSourceEvents();

  /**
   * Set the source triggering source events/methods.
   *
   * @param string $input
   *   Input.
   *
   * @return $this
   *   The Gamification entity.
   */
  public function setSourceEvents($input);

  /**
   * Returns the limits.
   *
   * @return string
   *   The source entity bundle.
   */
  public function getLimits();

  /**
   * Set the limits.
   *
   * @param string $limits
   *   Limits.
   *
   * @return $this
   *   The Gamification entity.
   */
  public function setLimits($limits);

  /**
   * Call execution callback from plugin definition.
   *
   * @param string $method
   *   Triggering event string.
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   *   Content entity which triggered this event.
   */
  public function execute($method, $entity);

  /**
   * Show a message accordingly to status, after creating/updating an entity.
   *
   * @param int $status
   *   Status int, returned after creating/updating an entity.
   */
  public function statusMessage($status);

}
