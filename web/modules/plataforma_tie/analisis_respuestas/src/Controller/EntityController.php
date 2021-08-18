<?php


namespace Drupal\analisis_respuestas\Controller;


use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EditorialContentEntityBase;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\user\EntityOwnerInterface;
use Drupal\user\EntityOwnerTrait;

class EntityController extends ControllerBase
{

  public function __construct(EntityTypeManagerInterface $entityTypeManager)
  {
    $this->entityTypeManager = $entityTypeManager;
  }

  public static function create(ContainerInterface $container)
  {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  public function entityLoad() {

    $user = $this->entityTypeManager->getStorage('user')->load(1);
    dpm($user, 'user');

    $users = $user = $this->entityTypeManager->getStorage('user')->loadMultiple();
    dpm($users, 'users');

    $node = $this->entityTypeManager->getStorage('node')->load(1);
    dpm($node, 'node');

    $nodes = $this->entityTypeManager->getStorage('node')->loadMultiple([1,2,3]);
    dpm($nodes, 'nodes');

    return ['#markup' => 'Ruta que carga entidades'];
  }

  public function entityCreate() {
    $i=0;
    $result=[];
    $array_adopcion =[];
    $proceso1 =[]; $proceso2 =[]; $proceso3 =[]; $proceso4 =[]; $proceso5 =[]; $proceso6 =[]; $proceso7 =[]; $proceso8 =[]; $proceso9 =[];
    $proceso10 =[]; $proceso11 =[]; $proceso12 =[]; $proceso13 =[]; $proceso14 =[]; $proceso15 =[]; $proceso16 =[];
    $array_apropiacion = [];
    $proceso1_ap =[]; $proceso2_ap =[]; $proceso3_ap =[]; $proceso4_ap =[]; $proceso5_ap =[]; $proceso6_ap =[]; $proceso7_ap =[]; $proceso8_ap =[]; $proceso9_ap =[];
    $proceso10_ap =[]; $proceso11_ap =[]; $proceso12_ap =[]; $proceso13_ap =[]; $proceso14_ap =[]; $proceso15_ap =[]; $proceso16_ap =[];

    $storage = \Drupal::entityTypeManager()->getStorage('quiz_result');
    $query = $storage->getQuery();
    $ids_result = $query->execute();
    $quiz_results = !empty($ids_result) ? $storage->loadMultiple($ids_result):[];
    foreach ($quiz_results as $quiz_result){
      if($quiz_result->getAccount()->id() == $this->currentUser()->id()){
        $ids_answer = \Drupal::entityTypeManager()->getStorage('quiz_result_answer')->getQuery()->execute();
        $quiz_answer = !empty($ids_answer) ? \Drupal::entityTypeManager()->getStorage('quiz_result_answer')->loadMultiple($ids_answer):[];
        foreach ($quiz_answer as $answers){
          if($answers->referencedEntities()[0]->id() == $quiz_result->id()){
            $id_question = $answers->referencedEntities()[1]->id(); // Extrae ID de las preguntas
            $storage = \Drupal::entityTypeManager()->getStorage('quiz_question');
            $query = $storage->getQuery();
            $ids_questions = $query->execute();
            $questions = !empty($ids_questions) ? $storage->loadMultiple($ids_questions):[];
            foreach ($questions as $question){
              $id_qs = $question->id();
              if($id_qs == $id_question){
                $tipo_qs = $question->get('field_tipo')->entity;
                $tipo = $tipo_qs ? $tipo_qs->get('field_tipo_form')[0]->getString() : NULL;
                //dpm($tipo);
                $clase_qs = $question->get('field_clasificacion')->entity;
                $clase = $clase_qs ? $clase_qs->get('field_id_estatico_terms')[0]->getString() :NULL;
                if($clase == 'P') {
                  $proceso_txt = $question->get('field_categoria_proceso')->referencedEntities()[1];
                  $process = $proceso_txt ? $proceso_txt->get('field_id_proceso')[0]->getString(): NULL;
                  $score = $answers->getPoints();
                  //-----------------------------------------------------------------------------------------
                  if($tipo == 'adopcion'){
                    if($process == 'proceso1'){$log = sizeof($proceso1); $proceso1[$log] = $score;}
                    elseif($process == 'proceso2'){$log = sizeof($proceso2);$proceso2[$log] = $score;}
                    elseif($process == 'proceso3'){$log = sizeof($proceso3);$proceso3[$log] = $score;}
                    elseif($process == 'proceso4'){$log = sizeof($proceso4);$proceso4[$log] = $score;}
                    elseif($process == 'proceso5'){$log = sizeof($proceso5);$proceso5[$log] = $score;}
                    elseif($process == 'proceso6'){$log = sizeof($proceso6);$proceso6[$log] = $score;}
                    elseif($process == 'proceso7'){ $log = sizeof($proceso7);$proceso7[$log] = $score;}
                    elseif($process == 'proceso8'){ $log = sizeof($proceso8);$proceso8[$log] = $score;}
                    elseif($process == 'proceso9'){ $log = sizeof($proceso9);$proceso9[$log] = $score;}
                    elseif($process == 'proceso10'){$log = sizeof($proceso10);$proceso10[$log] = $score;}
                    elseif($process == 'proceso11'){$log = sizeof($proceso11);$proceso11[$log] = $score;}
                    elseif($process == 'proceso12'){$log = sizeof($proceso12);$proceso12[$log] = $score;}
                    elseif($process == 'proceso13'){$log = sizeof($proceso13);$proceso13[$log] = $score;}
                    elseif($process == 'proceso14'){$log = sizeof($proceso14);$proceso14[$log] = $score;}
                    elseif($process == 'proceso15'){$log = sizeof($proceso15);$proceso15[$log] = $score;}
                    else{$log = sizeof($proceso16);$proceso16[$log] = $score;}
                  }
                  else{

                    if($process == 'proceso1'){
                      $log = sizeof($proceso1_ap);
                      $proceso1_ap[$log] = $score;
                    }
                    elseif($process == 'proceso2'){
                      $log = sizeof($proceso2_ap);
                      $proceso2_ap[$log] = $score;
                    }
                    elseif($process == 'proceso3'){
                      $log = sizeof($proceso3_ap);
                      $proceso3_ap[$log] = $score;
                    }
                    elseif($process == 'proceso4'){
                      $log = sizeof($proceso4_ap);
                      $proceso4_ap[$log] = $score;
                    }
                    elseif($process == 'proceso5'){
                      $log = sizeof($proceso5_ap);
                      $proceso5_ap[$log] = $score;
                    }
                    elseif($process == 'proceso6'){
                      $log = sizeof($proceso6_ap);
                      $proceso6_ap[$log] = $score;
                    }
                    elseif($process == 'proceso7'){
                      $log = sizeof($proceso7_ap);
                      $proceso7_ap[$log] = $score;
                    }
                    elseif($process == 'proceso8'){
                      $log = sizeof($proceso8_ap);
                      $proceso8_ap[$log] = $score;
                    }
                    elseif($process == 'proceso9'){
                      $log = sizeof($proceso9_ap);
                      $proceso9_ap[$log] = $score;
                    }
                    elseif($process == 'proceso10'){
                      $log = sizeof($proceso10_ap);
                      $proceso10_ap[$log] = $score;
                    }
                    elseif($process == 'proceso11'){
                      $log = sizeof($proceso11_ap);
                      $proceso11_ap[$log] = $score;
                    }
                    elseif($process == 'proceso12'){
                      $log = sizeof($proceso12_ap);
                      $proceso12_ap[$log] = $score;
                    }
                    elseif($process == 'proceso13'){
                      $log = sizeof($proceso13_ap);
                      $proceso13_ap[$log] = $score;
                    }
                    elseif($process == 'proceso14'){
                      $log = sizeof($proceso14_ap);
                      $proceso14_ap[$log] = $score;
                    }
                    elseif($process == 'proceso15'){
                      $log = sizeof($proceso15_ap);
                      $proceso15_ap[$log] = $score;
                    }
                    else{
                      $log = sizeof($proceso16_ap);
                      $proceso16_ap[$log] = $score;
                    }
                  }

                  //-----------------------------------------------------------------------------------------


                }elseif($clase == 'TC'){
                  $categoria_proceso = $question->get('field_categoria_proceso')->getValue();
                  $categoria = $categoria_proceso[0]['target_id'] ?? NULL;

                }else{

                }
                //ARRAY DE ADOPCION
                $array_adopcion[0]=$proceso1; $array_adopcion[1]=$proceso2; $array_adopcion[2]=$proceso3; $array_adopcion[3]=$proceso4;
                $array_adopcion[4]=$proceso5; $array_adopcion[5]=$proceso6; $array_adopcion[6]=$proceso7; $array_adopcion[7]=$proceso8;
                $array_adopcion[8]=$proceso9; $array_adopcion[9]=$proceso10; $array_adopcion[10]=$proceso11; $array_adopcion[11]=$proceso12;
                $array_adopcion[12]=$proceso13; $array_adopcion[13]=$proceso14; $array_adopcion[14]=$proceso15; $array_adopcion[15]=$proceso16;

                //ARRAY DE APROPIACION

                $array_apropiacion[0] = $proceso1_ap; $array_apropiacion[1] = $proceso2_ap; $array_apropiacion[2] = $proceso3_ap; $array_apropiacion[3] = $proceso4_ap;
                $array_apropiacion[4] = $proceso5_ap; $array_apropiacion[5] = $proceso6_ap; $array_apropiacion[6] = $proceso7_ap; $array_apropiacion[7] = $proceso8_ap;
                $array_apropiacion[8] = $proceso9_ap; $array_apropiacion[9] = $proceso10_ap; $array_apropiacion[10] = $proceso11_ap; $array_apropiacion[11] = $proceso12_ap;
                $array_apropiacion[12] = $proceso13_ap; $array_apropiacion[13] = $proceso14_ap; $array_apropiacion[14] = $proceso15_ap; $array_apropiacion[15] = $proceso16_ap;
              }
            }
          }
        }
      }
    }

    foreach ($array_adopcion as $proceso){
      $result[$i]=$this->calculo($proceso);
      $i++;
      $form = " Adopción Tecnológica ";
    }

    $nombre_user = \Drupal::currentUser()->getAccountName();


    $this->adopcionCreateIndicadores($result,$nombre_user,$form);



    //$this->adopcionCreateIndicadores($rm);
    return ['#markup' => 'Ruta que crear entidades'];
  }

  public function  calculo($proceso){
    $suma =0;
    if($proceso == []){
      return;
    }
    foreach ($proceso as $result){
      $suma = $suma +$result;
    }
    $longitud = sizeof($proceso);
    $valor_maximo = $longitud * 60; //cambiar 60, por valor de maximo score
    $porcentaje = ($suma*100)/$valor_maximo;

    return $porcentaje;

  }



  public function MatrizDeCalor($categoria_q, $proceso_q){

    $storage =\Drupal::entityTypeManager()->getStorage('group');
    $query = $storage->getQuery();
    $ids =$query->execute();
    $groups = !empty($ids) ? $storage->loadMultiple($ids):[];
    foreach ($groups as $group){
      $tipo_empresa_group = $group->get('field_tipo')->getValue()[0]['target_id'] ?? NULL;
      $tamano_empresa_group = $group->get('field_tamano_de_empresa')->getValue()[0]['target_id'] ?? NULL;
      $storage = \Drupal::entityTypeManager()->getStorage('node');
      $query = $storage->getQuery()
        ->condition('type','matriz_de_calor')
        ->condition('status',1);
      $ids = $query->execute();
      $nodes = !empty($ids) ? $storage->loadMultiple($ids):[];
      foreach ($nodes as $node){
        $rol_node = $node->get('field_rol_del_evaluador')->entity->get('field_rol')->referencedEntities()[0]->id();
        $tipo_empresa_node = $node->get('field_tipo_de_empresa')->getValue()[0]['target_id'] ?? NULL;
        $tamano_empresa_node = $node->get('field_tamano_de_empresa')->getValue()[0]['target_id'] ?? NULL;
        $rol = \Drupal::currentUser()->getRoles()[1];
        if($tipo_empresa_group == $tipo_empresa_node && $tamano_empresa_group == $tamano_empresa_node && $rol == $rol_node){
          $cumple =1;
          $categoria_proceso_node = $node->get('field_categoria_proceso_y_critic')->referencedEntities();
          foreach ($categoria_proceso_node as $paragraph){
            $categoria_node =$paragraph->get('field_categoria_proceso')->getValue()[0]['target_id'] ?? NULL;
            $proceso_node =$paragraph->get('field_categoria_proceso')->getValue()[1]['target_id'] ?? NULL;
            if($categoria_node == $categoria_q && $proceso_node == $proceso_q){
              //$criticidad = $paragraph->get('field_criticidad')->entity->get('field_id_estatico')->getString();
              $criticidad_value = $paragraph->get('field_criticidad')->entity->get('field_valor')->getValue()[0]['value'];
              return $criticidad_value;
            }
          }
        }else{
          $cumple=0;
        }
      }
    }
    //return $criticidad;

  }



  public function adopcionCreateIndicadores($p,$name, $form){
    $values = [
      'title' => "Analisis de" . $form . "por " . $name,
      'field_proceso_1'=> $p[0],
      'field_proceso_2'=> $p[1],
      'field_proceso_3'=> $p[2],
      'field_proceso_4'=> $p[3],
      'field_proceso_5'=> $p[4],
      'field_proceso_6'=> $p[5],
      'field_proceso_7'=> $p[6],
      'field_proceso_8'=> $p[7],
      'field_proceso_9'=> $p[8],
      'field_proceso_10'=> $p[9],
      'field_proceso_11'=> $p[10],
      'field_proceso_12'=> $p[11],
      'field_proceso_13'=> $p[12],
      'field_proceso_14'=> $p[13],
      'field_proceso_15'=> $p[14],
      'field_proceso_16'=> $p[15],
      'type' => 'analisis_de_respuesta'

    ];

    $node = $this->entityTypeManager->getStorage('node')->create($values);
    $node->save();
    dpm($node, 'node');

    $values = [
      'type' => 'group_content_type_0b89a4774c92a',
      'gid' => 4,
      'entity_id' => ['target_id' => $node->id()],
    ];

    $entity = $this->entityTypeManager->getStorage('group_content')->create($values);
    $entity->save();

  }

  public function entityEdit() {

//    $values = [
//      'title' => 'Articulo creado en codigo',
//      'type' => 'article'
//    ];
//
//    $node = $this->entityTypeManager->getStorage('node')->create($values);
//    $node->save();

    /** @var NodeInterface $node */
    $node = $this->entityTypeManager->getStorage('node')->load(9);
//    $node->set('field_texto', 'Este es el campo field_texto');
//    $node->set('body', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec non scelerisque nulla. Vestibulum feugiat lacus dapibus sapien condimentum vehicula. Etiam sit amet dignissim ante, non dignissim tellus. Fusce vulputate lacus sit amet euismod laoreet. Phasellus sed risus sed neque consequat vulputate. Suspendisse dictum ligula eu egestas tristique. Cras scelerisque commodo urna, nec vehicula urna placerat at. Morbi vehicula nec sem nec vulputate. Donec tincidunt ex in nunc egestas, quis imperdiet ipsum tempor. Sed vestibulum quam vel diam condimentum, elementum viverra purus euismod. Morbi ut massa sodales, viverra nisl ut, aliquam sem. ');
//    $node->save();
//    $campo = $node->get('field_texto')->value;
//    dpm($campo, 'campo');

    $terms = $this->entityTypeManager->getStorage('taxonomy_term')->loadMultiple();
//    dpm($terms, 'terms');

//    $node->get('field_tags')->appendItem($terms[4]);
//    $node->get('field_tags')->appendItem($terms[2]);
//    $node->get('field_tags')->appendItem($terms[3]);
//
    $node->get('field_tags')->removeItem(0);
    $node->save();
//    dpm($values, 'values');


    return ['#markup' => 'Ruta que editar entidades'];
  }

  public function entityDelete() {
    $node = $this->entityTypeManager->getStorage('node')->load(9);
    $node->delete();
    return ['#markup' => 'Ruta que elimina entidades'];
  }




  public function entityQuery() {
    $questions = $this->lista_de_preguntas();
    //dpm($questions);


    return ['#markup' => 'Ruta que consulta entidades'];
  }


  public function lista_de_preguntas(){

    return ['#markup' => 'Ruta que consulta entidades'];
  }


}
