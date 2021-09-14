<?php

namespace Drupal\gamification\Plugin;

use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Field\FieldDefinitionInterface;

/**
 * Plugin manager for finding and using gamification widgets.
 */
class GamificationWidgetManager extends DefaultPluginManager {

  /**
   * Constructs an GamificationWidgetManager object.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/Gamification/Widget', $namespaces, $module_handler, 'Drupal\gamification\Plugin\GamificationWidgetInterface', 'Drupal\gamification\Annotation\GamificationWidget');
    $this->alterInfo('gamification_info');
    $this->setCacheBackend($cache_backend, 'gamification_info_plugins');
  }

  public function getPlugin($plugin_id = 'default_widget') {
    return $this->createInstance($plugin_id);
  }

  /**
   * Get gamification widgets to populate select in Gamification form.
   */
  public function getGamificationWidgets() {
    $instances = $configuration = [];
    $plugin_definitions = $this->getDefinitions();
    usort($plugin_definitions, function ($definition1, $definition2) {
      return $definition1['weight'] <=> $definition1['weight'];
    });
    $widget = $this->createInstance('default_widget')->getPlugin();
    if ($widget->checkAccess()) {
      $instances[] = $widget;
    }
    foreach ($this->getDefinitions() as $widget) {
      if ($widget['id'] === 'default_widget') {
        continue;
      }
      $widget = $this->createInstance($widget['id'], $configuration)->getPlugin();
      if ($widget->checkAccess()) {
        $instances[] = $widget;
      }
    }
    return $instances;
  }

  /**
   * Get actions from gamification widget id.
   *
   * @param string $widget
   *   Gamification widget id.
   *
   * @return array $actions
   *   Array with widget actions.
   */
  public function getActionsFromWidget($widget = 'default_widget') {
    $actions = $this->getPlugin($widget)->getPlugin()->getActions();
    return $actions;
  }

  /**
   * Get options from gamification widget id.
   *
   * @param string $widget
   *   Gamification widget id.
   *
   * @return array
   *   Array with widget options.
   */
  public function getWidgetOptions($widget = 'default_widget') {
    return $this->getPlugin($widget)->getPlugin()->getWidgetOptions();
  }

  /**
   * Get source events from gamification widget id.
   *
   * @param string $widget
   *   Gamification widget id.
   *
   * @return array $result
   *   Array with widget source events.
   */
  public function getWidgetSourceValues($widget = 'default_widget') {
    $plugin = $this->getPlugin($widget)->getPlugin();
    $result = [];
    $result['source_entity_type_id'] = $plugin->getSourceEntityTypeId();
    $result['source_entity_bundle'] = $plugin->getSourceEntityBundle();
    $result['source_events'] = $plugin->getSourceEvents();
    return $result;
  }

  /**
   * Remove entry from form array in gamification form.
   *
   * @param array $form
   *   Form array.
   * @param array $indexes
   *   Array with indexes that should be removed from form array.
   *
   * @return array $form
   *   Form array.
   */
  protected function removeByIndex($form, $indexes) {
    if (!is_array($indexes)) {
      return $form;
    }

    $array = & $form;
    $index_count = count($indexes);
    for ($i = 0; $i < $index_count; $i++) {
      if ($i === $index_count - 1) {
        unset($array[$indexes[$i]]);
      } elseif (is_array($array[$indexes[$i]])) {
        $array = & $array[$indexes[$i]];
      } else {
        return $form;
      }
    }

    return $form;
  }

  /**
   * Hide fields from form array in gamification form.
   *
   * @param string $widget
   *   Gamification widget id.
   * @param array $form
   *   Form array.
   *
   * @return array $form
   *   Form array.
   */
  public function hideFieldsFromForm($widget = 'default_widget', $form) {
    $fields_to_hide = $this->getPlugin($widget)->getPlugin()->getFieldsToHide();
    if (empty($fields_to_hide)) {
      return $form;
    }

    foreach ($fields_to_hide as $fh) {
      $path = explode('.', $fh);
      $form = $this->removeByIndex($form, $path);
    }

    return $form;
  }

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
  public function execute($gamification_config_entity, $method = '', $entity = NULL) {
    $widget = $gamification_config_entity->getWidgetValue();
    if (empty($widget)) {
      $widget = 'default_widget';
    }
    return $this->getPlugin($widget)->getPlugin()->execute($gamification_config_entity, $method, $entity);
  }

}
