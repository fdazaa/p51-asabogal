<?php

namespace Drupal\gamification\Entity;

use Drupal\Core\Messenger\MessengerTrait;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\gamification\GamificationInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines the Gamification entity.
 *
 * @ConfigEntityType(
 *   id = "gamification",
 *   label = @Translation("Gamification"),
 *   handlers = {
 *     "list_builder" = "Drupal\gamification\Controller\GamificationListBuilder",
 *     "form" = {
 *       "add" = "Drupal\gamification\Form\GamificationForm",
 *       "edit" = "Drupal\gamification\Form\GamificationForm",
 *       "delete" = "Drupal\gamification\Form\GamificationDeleteForm",
 *     }
 *   },
 *   config_prefix = "gamification",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "title" = "title",
 *     "widget" = "widget",
 *     "action" = "action",
 *     "points" = "points",
 *     "widget_options" = "widget_options",
 *     "source_entity_type_id" = "source_entity_type_id",
 *     "source_bundle" = "source_bundle",
 *     "source_events" = "source_events",
 *     "limits" = "limits",
 *   },
 *   links = {
 *     "edit-form" = "/admin/config/gamification/{gamification}",
 *     "delete-form" = "/admin/config/gamification/{gamification}/delete",
 *   },
 *   config_export = {
 *     "id",
 *     "title",
 *     "widget",
 *     "action",
 *     "points",
 *     "widget_options",
 *     "source_entity_type_id",
 *     "source_bundle",
 *     "source_events",
 *     "limits",
 *   }
 * )
 */
class GamificationEntity extends ConfigEntityBase implements GamificationInterface {

  use StringTranslationTrait, MessengerTrait;

  /**
   * Gamification entity id.
   *
   * @var string
   */
  protected $id;

  /**
   * Gamification entity title.
   *
   * @var string
   */
  protected $title;

  /**
   * Gamification widget.
   *
   * @var string
   */
  protected $widget;

  /**
   * Gamification points attributed to user after action.
   *
   * @var string
   */
  protected $points;

  /**
   * Gamification source entity type id.
   *
   * @var string
   */
  protected $source_entity_type_id;

  /**
   * Gamification source bundle.
   *
   * @var string
   */
  protected $source_bundle;

  /**
   * Returns the entity title.
   *
   * @return string
   *   The entity title.
   */
  public function getTitle() {
    return $this->get('title');
  }

  /**
   * Sets the entity title.
   *
   * @param string $title
   *   Title.
   *
   * @return $this
   *   The Gamification entity.
   */
  public function setTitle($title) {
    $this->set('title', $title);
    return $this;
  }

  /**
   * Returns the gamification widget id.
   *
   * @return string
   *   The widget id.
   */
  public function getWidgetValue() {
    return $this->get('widget');
  }

  /**
   * Sets the gamification widget.
   *
   * @param string $widget
   *   Gamification widget title.
   *
   * @return $this
   *   The Gamification entity.
   */
  public function setWidgetValue($widget) {
    $this->set('widget', $widget);
    return $this;
  }

  /**
   * Returns the gamification widget action.
   *
   * @return string
   *   The widget action.
   */
  public function getAction() {
    return $this->get('action');
  }

  /**
   * Sets the gamification widget action.
   *
   * @param string $widget
   *   Gamification widget title.
   *
   * @return $this
   *   The Gamification entity.
   */
  public function setAction($widget) {
    $this->set('action', $widget);
    return $this;
  }

  /**
   * Returns the entity points.
   *
   * @return string
   *   The entity points.
   */
  public function getPoints() {
    return $this->get('points');
  }

  /**
   * Sets the entity points.
   *
   * @param string $points
   *   Points.
   *
   * @return $this
   *   The Gamification entity.
   */
  public function setPoints($points) {
    $this->set('points', $points);
    return $this;
  }

  /**
   * Returns the widget options values.
   *
   * @return array
   *   The widget options value.
   */
  public function getWidgetOptionsValue() {
    return $this->get('widget_options');
  }

  /**
   * Set the widget options values.
   *
   * @param array $attributes
   *   Widget options value.
   *
   * @return $this
   *   The Gamification entity.
   */
  public function setWidgetOptionsValue($attributes = []) {
    $this->set('widget_options', $attributes);
    return $this;
  }

  /**
   * Returns the widget options keys.
   *
   * @return array
   *   The widget options keys.
   */
  public function getWidgetOptions() {
    $gamificationWidgetManager = \Drupal::service('plugin.manager.gamification.widget');
    $widget = $this->getWidgetValue();
    if (empty($widget)) {
      return [];
    }
    return $gamificationWidgetManager->getWidgetOptions($widget);
  }

  /**
   * Returns the source entity type.
   *
   * @return string
   *   The source entity type id.
   */
  public function getSourceEntityTypeId() {
    return $this->get('source_entity_type_id');
  }

  /**
   * Set the source entity type.
   *
   * @param string $input
   *   Input.
   * 
   * @return $this
   *   The Gamification entity.
   */
  public function setSourceEntityId($input) {
    $this->set('source_entity_type_id', $input);
    return $this;
  }

  /**
   * Returns the source bundle.
   *
   * @return string
   *   The source entity bundle.
   */
  public function getSourceBundle() {
    return $this->get('source_bundle');
  }

  /**
   * Set the source bundle.
   *
   * @param string $input
   *   Input.
   *
   * @return $this
   *   The Gamification entity.
   */
  public function setSourceBundle($input) {
    $this->set('source_bundle', $input);
    return $this;
  }

  /**
   * Returns the source triggering source events/methods.
   *
   * @return string
   *   The source entity bundle.
   */
  public function getSourceEvents() {
    return $this->get('source_events');
  }

  /**
   * Set the source triggering source events/methods.
   *
   * @param string $input
   *   Input.
   *
   * @return $this
   *   The Gamification entity.
   */
  public function setSourceEvents($input) {
    $this->set('source_events', $input);
    return $this;
  }

  /**
   * Returns the limits.
   *
   * @return string
   *   The source entity bundle.
   */
  public function getLimits() {
    return $this->get('limits');
  }

  /**
   * Set the limits.
   *
   * @param string $limits
   *   Limits.
   *
   * @return $this
   *   The Gamification entity.
   */
  public function setLimits($limits) {
    $this->set('limits', $limits);
    return $this;
  }

  /**
   * Call execution callback from plugin definition.
   *
   * @param string $method
   *   Triggering event string.
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   *   Content entity which triggered this event.
   */
  public function execute($method = '', $entity = NULL) {
    $gamificationWidgetManager = \Drupal::service('plugin.manager.gamification.widget');
    if (empty($action)) {
      $action = $this->getAction();
    }
    $gamificationWidgetManager->execute($this, $method, $entity);
  }

  /**
   * Show a message accordingly to status, after creating/updating an entity.
   *
   * @param int $status
   *   Status int, returned after creating/updating an entity.
   */
  public function statusMessage($status) {
    if ($status) {
      $this->messenger()->addMessage($this->t('Saved the %label entity.', ['%label' => $this->getTitle()]));
    }
    else {
      $this->messenger()->addMessage($this->t('The %label entity was not saved.', ['%label' => $this->getTitle()]));
    }
  }

}
