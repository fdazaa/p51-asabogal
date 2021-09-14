<?php

namespace Drupal\gamification\Plugin;

use Drupal\gamification\Plugin\GamificationWidgetInterface;
use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Base for a gamification widget.
 */
abstract class GamificationWidgetBase extends PluginBase implements GamificationWidgetInterface, ContainerFactoryPluginInterface {

  /**
   * The module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, array $plugin_definition, ModuleHandlerInterface $module_handler, AccountInterface $current_user, EntityTypeManagerInterface $entity_type_manager, Connection $database) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->moduleHandler = $module_handler;
    $this->currentUser = $current_user;
    $this->entityTypeManager = $entity_type_manager;
    $this->database = $database;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('module_handler'),
      $container->get('current_user'),
      $container->get('entity_type.manager'),
      $container->get('database')
    );
  }

  /**
   * Return the plugin.
   */
  public function getPlugin() {
    return $this;
  }

  /**
   * Get the plugin ID.
   */
  public function getId() {
    return $this->pluginDefinition['id'];
  }

  /**
   * Get the plugin label.
   */
  public function getLabel() {
    return $this->pluginDefinition['label'];
  }

  /**
   * Get the plugin weight.
   */
  public function getWeight() {
    return $this->pluginDefinition['weight'];
  }

  /**
   * Get the required modules to use this widget.
   */
  public function getModules() {
    return $this->pluginDefinition['modules'];
  }

  /**
   * Get the widget actions.
   */
  public function getActions() {
    return $this->pluginDefinition['actions'];
  }

  /**
   * Get the widget options.
   */
  public function getWidgetOptions() {
    return $this->pluginDefinition['widget_options'];
  }

  /**
   * Get fields that should be hidden in gamification configuration form.
   */
  public function getFieldsToHide() {
    return $this->pluginDefinition['hide_fields'];
  }

  /**
   * Get the source entity type id.
   */
  public function getSourceEntityTypeId() {
    return $this->pluginDefinition['source_entity_type_id'];
  }

  /**
   * Get the source entity bundles.
   */
  public function getSourceEntityBundle() {
    return $this->pluginDefinition['source_entity_bundle'];
  }

  /**
   * Get the source trigger events.
   */
  public function getSourceEvents() {
    return $this->pluginDefinition['source_events'];
  }

  /**
   * Check if a plugin should be visible in gamification configurations. 
   */
  public function checkAccess() {
    $modules = $this->getModules();
    // Check if all declared modules are enabled. Otherwise, we don't need to show this plugin in selection list.
    foreach ($modules as $module) {
      if (!$this->moduleHandler->moduleExists($module)) {
        return FALSE;
      }
    }
    return TRUE;
  }

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
  public function createGamificationLog($gamification_config_entity, $method = '', $entity, $points = 0) {
    // Insert into gamification logs.
    $result = $this->database->insert('gamification_log')
      ->fields([
        'type' => 'gamification_log',
        'gamification_entity_id' => $gamification_config_entity->id(),
        'entity_type' => $gamification_config_entity->getSourceEntityTypeId(),
        'bundle' => $entity->bundle(),
        'entity_id' => intval($entity->id()),
        'method' => $method,
        'points' => $points,
        'uid' => intval($this->currentUser->id()),
        'created' => time(),
      ])
      ->execute();
    return TRUE;
  }

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
  public function checkLimits($gamification_config_entity, $method, $entity) {
    $limits = $gamification_config_entity->getLimits();

    // Check if it is supposed to limit attribution of points by entity id or bundle.
    if (!empty($limits['entity_id']) || !empty($limits['bundle'])) {
      $values = [];
      $values['gamification_entity_id'] = $gamification_config_entity->id();
      $values['entity_type'] = $gamification_config_entity->getSourceEntityTypeId();
      $values['uid'] = $this->currentUser->id();

      // Check if points should be attributed only once per entity id or bundle.
      if (!empty($limits['entity_id'])) {
        $values['entity_id'] = intval($entity->id());
      } else {
        if (!empty($limits['bundle'])) {
          $values['bundle'] = $entity->bundle();
        }
      }

      if ($this->checkGamificationLogExistsByValues($values)) {
        return FALSE;
      }
    }

    // Check if it is supposed to limit attribution of points by time.
    if (!empty($limits['time'])) {
      $time = intval($limits['time']);
      $values = [];
      $values['gamification_entity_id'] = $gamification_config_entity->id();
      $values['uid'] = $this->currentUser->id();

      // Points should be given only once.
      if ($time === 0) {
        if ($this->checkGamificationLogExistsByValues($values)) {
          return FALSE;
        }
      }

      // Points should be given only attributed according to the time selected in gamification configuration.
      if ($time > 0) {
        $trigger_time = time() - $time;
        if ($this->checkGamificationLogExistsByTime($values, $trigger_time)) {
          return FALSE;
        }
      }
    }

    return TRUE;
  }

  /**
   * Check if exists a gamification log with specific conditions.
   *
   * @param array $values
   *   Values to be used in conditions.
   *
   * @return bool
   *   True if exists any result, otherwise return false.
   */
  public function checkGamificationLogExistsByValues($values) {
    $query = $this->database->select('gamification_log', 'g')->fields('g', ['gid']);
    foreach ($values as $k => $v) {
      $query->condition('g.' . $k, $v);
    }

    $results = $query->range(0, 1)->execute()->fetchField();

    return !empty($results);
  }

  /**
   * Check if exists a gamification log with specific conditions, including
   * the created value.
   *
   * @param array $values
   *   Values to be used in conditions.
   * @param string $time_ago
   *   Time that must be compared with created value.
   *
   * @return bool
   *   True if exists any result, otherwise return false.
   */
  public function checkGamificationLogExistsByTime($values, $time_ago) {
    $query = $this->database->select('gamification_log', 'g')->fields('g', ['gid']);
    foreach ($values as $k => $v) {
      $query->condition('g.' . $k, $v);
    }

    $results = $query->condition('created', $time_ago, '>')
      ->range(0, 1)
      ->execute()
      ->fetchField();

    return !empty($results);
  }

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
  public function getGamificationLogByValues($values, $fields) {
    $query = $this->database->select('gamification_log', 'g')->fields('g', $fields);
    foreach ($values as $k => $v) {
      $query->condition('g.' . $k, $v);
    }

    $result = $query->orderBy('g.created', 'ASC')
      ->range(0, 1)
      ->execute()
      ->fetchAll();

    return !empty($result) && is_array($result) ? reset($result) : $result;
  }

}
