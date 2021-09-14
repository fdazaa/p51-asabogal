<?php

namespace Drupal\gamification\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a data type of structured data.
 *
 * Plugin Namespace: Plugin\Gamification\Widget.
 *
 * @see \Drupal\structured_data\StructuredDataManager
 * @see plugin_api
 *
 * @Annotation
 */
class GamificationWidget extends Plugin {

  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The label of gamification widget.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $label;

  /**
   * The plugin weight.
   *
   * @var int
   */
  public $weight;

}
