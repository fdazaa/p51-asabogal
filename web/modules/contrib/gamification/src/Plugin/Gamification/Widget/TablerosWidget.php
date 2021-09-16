<?php

namespace Drupal\gamification\Plugin\Gamification\Widget;

use Drupal\gamification\Plugin\GamificationWidgetUserpointsBase;

/**
 * Provides a user login widget.
 *
 * @GamificationWidget(
 *   id = "tableros",
 *   label = @Translation("Tableros de Categorias"),
 *   modules = {
 *     "userpoints",
 *   },
 *   weight = 0,
 *   entity_type = "content",
 *   source_entity_type_id = "node",
 *   source_entity_bundle = {
 *     "tablero_de_resultados_de_categor",
 *   },
 *   source_events = {
 *     "entity_update" = "entity_update",
 *   },
 *   actions = {
 *     "tablero_de_resultados_de_categor" = @Translation("Variaciones"),
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
class TablerosWidget extends GamificationWidgetUserpointsBase {

  /**
   * {@inheritdoc}
   */
  public function execute($gamification_config_entity = NULL, $method = '', $entity = NULL) {
    $j=0;

    $points = $gamification_config_entity->getPoints();
    if (empty($points)) {
      return FALSE;
    }

    $variacion = $this->variacion();
    $log = sizeof($variacion);
    for ($i=0;$i<$log;$i++){
      if($variacion[$i][1]){
        $variacion_revision = $this->variacionrevision($variacion[$i][1]);
        if($variacion_revision<$variacion[$i][0]){
          $j++;
          }
      }
    }
    $points = $points*$j;

    return $this->executeTransaction($gamification_config_entity, $method, $entity, intval($points));
  }

  function variacion(){
    $i=0;
    $nodes=[];
    $storage = \Drupal::entityTypeManager()->getStorage('node');
    $query = $storage ->getQuery()
      ->condition('type', 'tablero_de_resultados_de_categor')
      ->condition('status',1);
    $ids = $query->execute();
    $tableros = !empty($ids) ? $storage->loadMultiple($ids):NULL;
    foreach ($tableros as $tablero){
      $id_autor = $tablero->uid->target_id;
      $id_user = \Drupal::currentUser()->id();
      if($id_autor == $id_user){
        $variacion = $tablero->get('field_variacion')->value;
        $nodes[$i][0]=$variacion;
        $revision = \Drupal::entityTypeManager()->getStorage('node')->revisionIds($tablero);
        $log = sizeof($revision);
        $nodes[$i][1]=$revision[$log-2];
        $i++;
      }
    }
    return $nodes;
  }

  function variacionrevision($id_revision){
    $storage =\Drupal::entityTypeManager()->getStorage('node');
    $v_revision=$storage->loadRevision($id_revision)->get('field_variacion')->getValue()[0]['value'];
    return $v_revision;
  }

}
