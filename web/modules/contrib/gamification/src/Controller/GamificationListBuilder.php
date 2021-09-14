<?php

namespace Drupal\gamification\Controller;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Url;

/**
 * Provides a list of Gamification entities.
 */
class GamificationListBuilder extends ConfigEntityListBuilder {

  const TITLE = 'title';

  const WIDGET = 'widget';

  const ACTION = 'action';

  /**
   * Constructs the table header.
   *
   * @return array
   *   Table header
   */
  public function buildHeader() {
    $header[self::TITLE] = $this->t('Title');
    $header[self::WIDGET] = $this->t('Widget');
    $header[self::ACTION] = $this->t('Action');
    return $header + parent::buildHeader();
  }

  /**
   * Constructs the table rows.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   Gamification entity.
   *
   * @return \Drupal\Core\Entity\EntityListBuilder
   *   A render array structure of fields for this entity.
   */
  public function buildRow(EntityInterface $entity) {
    $row[self::TITLE] = $entity->get('title') . ' (' . $entity->id() . ')';
    $row[self::WIDGET] = $entity->getWidgetValue();
    $row[self::ACTION] = $entity->getAction();
    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  public function getDefaultOperations(EntityInterface $entity, $type = 'edit') {
    $operations = parent::getDefaultOperations($entity);

    return $operations;
  }

}
