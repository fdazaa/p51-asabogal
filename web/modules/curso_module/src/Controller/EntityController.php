<?php


namespace Drupal\curso_module\Controller;


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

//    $values = [
//      'title' => 'Nodo creado en codigo',
//      'type' => 'page'
//    ];
//
//    $node = $this->entityTypeManager->getStorage('node')->create($values);
//    $node->save();
//    dpm($node, 'node');

//    $values = [
//      'name' => 'test',
//      'mail' => 'ejemplo@escueladrupal.com',
//      'pass' => '122345',
//      'status' => 1,
//    ];
//
//    $user = $this->entityTypeManager->getStorage('user')->create($values);
//    $user->save();

//    $values = [
//      'name' => 'Drupal',
//      'vid' => 'tags',
//    ];
//
//    $term = $this->entityTypeManager->getStorage('taxonomy_term')->create($values);
//    $term->save();

    return ['#markup' => 'Ruta que crear entidades'];
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

    $storage=\Drupal::entityTypeManager()->getStorage('group');
    $query = $storage->getQuery();
    $ids = $query->execute();
    $groups =!empty($ids) ? $storage->loadMultiple($ids):[];

    $total_questions = [];

    foreach($groups as $group){
      //EXTRAE MIEMBROS DEL GRUPO
      $members = $group->getMembers();
      foreach($members as $member){
        $id_member = $member->getUser()->id(); //EXTRAE ID DE LOS MIEMBROS DEL GRUPO
        $id_general = \Drupal::currentUser()->id(); //EXTRAER ID DEL USUARIO LOG IN
        if($id_general == $id_member){
          //Acceso a la matriz de calor
          $storage_mc = \Drupal::entityTypeManager()->getStorage('node');
          $query_mc = $storage_mc->getQuery()
            ->condition('type','matriz_de_calor')
            ->condition('status',1);
          $ids_mc =$query_mc->execute();
          $nodes =  !empty($ids_mc) ? $storage_mc->loadMultiple($ids_mc) : [];
          //dpm($nodes);
          foreach ($nodes as $node){
            //EXTRAE EL ROL DE LA MATRIZ
            $field_rol = $node->get('field_rol_del_evaluador')->referencedEntities();
            foreach($field_rol as $rol_mc){
              $rol_r= $rol_mc->get('field_rol')->referencedEntities()[0]->id();
              $rol = \Drupal::currentUser()->getRoles()[1]; //EXTRAE EL ROL DEL USUARIO LOG IN
              if($rol_r == $rol){
                $rol_act[0]=1;
              }else{
                $rol_act[0]=0;
              }
            }
            $field_te = $node->get('field_tamano_de_empresa')->getValue()[0]['target_id']??NULL;
            $tamano = $group->get('field_tamano_de_empresa')->getValue()[0]['target_id']??NULL;
            $field_tpe = $node->get('field_tipo_de_empresa')->getValue()[0]['target_id']??NULL;
            $tipo = $group->get('field_tipo')->getValue()[0]['target_id']??NULL;
            if($field_te == $tamano && $field_tpe == $tipo && $rol_act[0]==1){

              $storage=\Drupal::entityTypeManager()->getStorage('quiz');
              $query = $storage->getQuery();
              $ids = $query->execute();
              $quizzes_to =!empty($ids) ? $storage->loadMultiple($ids):[];
              foreach($quizzes_to as $quizzes){
                $terms = $quizzes->get('quiz_terms')->referencedEntities();   
                $total_questions = [];
                $quiz_to = $quizzes->id();
                //$quiz_id = $this -> id();
                //if($quiz_to == $quiz_id){
                  if($terms != [] ){
                    foreach($terms as $term){
                      $tid = $term->get('quiz_question_tid')->referencedEntities()[0]->id();
                      $tid_2 = $term->get('quiz_question_tid')->entity;
                      $clasificacion = $tid_2->get('field_id_estatico_terms')[0]->getString();
                      //SI CLASIFICACION ES P - CONDICIONAL
                      if($clasificacion=='P'){
                        $storage = \Drupal::entityTypeManager()->getStorage('quiz_question');
                        $query = $storage->getQuery();
                        $ids = $query->execute();
                        $questions = !empty($ids) ? $storage->loadMultiple($ids):[];
                        //BARRE PREGUNTA POR PREGUNTA
                        foreach($questions as $quiz_question){
                          $field_clase = $quiz_question->get('field_clasificacion')->entity;
                          $clase = $field_clase ? $field_clase->get('field_id_estatico_terms')[0]->getString() : NULL;
                          //TIPO DE PREGUNTA
                          $field_tipo_q = $quiz_question ? $quiz_question->get('field_tipo')->referencedEntities()[0]->id() : NULL;
                          //TIPO DE FORMULARIO
                          $field_tipo_f = $quizzes ? $quizzes->get('field_tipo')->referencedEntities()[0]->id():NULL;
                           
                          //REVISA SI LA CLASE DE LA PREGUNTA ES P
                          if($clase=='P' && $field_tipo_q == $field_tipo_f){
                            dpm('INGRESA');
                            $filed_catp = $quiz_question->get('field_categoria_proceso')->getValue();
                            $categoria_q = $filed_catp[0]['target_id']?? NULL;
                            $proceso_q = $filed_catp[1]['target_id']?? NULL;
                            $field_value = $node->get('field_categoria_proceso_y_critic')->referencedEntities();
                            foreach($field_value as $paragraph){
                              $sub_field_value = $paragraph->get('field_categoria_proceso')->getValue();
                              $categoria_mc = $sub_field_value[0]['target_id']?? NULL;
                              $proceso_mc = $sub_field_value[1]['target_id']?? NULL;
                              if($categoria_q==$categoria_mc && $proceso_mc==$proceso_q){
                                $field_criticidad = $paragraph->get('field_criticidad')->entity;
                                $criticidad = $field_criticidad->get('field_id_estatico')->getString();
                                if($criticidad == 'ALTO'){
                                  $questions_alto[] = [
                                    'qqid'=>$quiz_question->get('qqid')->value,
                                    'tid'=>$tid,
                                    'type'=>$quiz_question->bundle(),
                                    'vid'=>$quiz_question->getRevisionId()
                                  ];                      
                                }
                                elseif($criticidad=='MEDIO'){
                                  $questions_medio[] = [
                                    'qqid'=>$quiz_question->get('qqid')->value,
                                    'tid'=>$tid,
                                    'type'=>$quiz_question->bundle(),
                                    'vid'=>$quiz_question->getRevisionId()
                                  ]; 
                                }
                                elseif($criticidad=='BAJO'){
                                  $questions_bajo[] = [
                                    'qqid'=>$quiz_question->get('qqid')->value,
                                    'tid'=>$tid,
                                    'type'=>$quiz_question->bundle(),
                                    'vid'=>$quiz_question->getRevisionId()
                                  ]; 
                                }                    
                              }
                            }
                          }
                        }

                      $questions_numbers_p = $term->get('quiz_question_number')[0]->value;
                      $tamano_alto = sizeof($questions_alto);
                      $tamano_medio = sizeof($questions_medio);
                      $tamano_bajo = sizeof($questions_bajo);

                      if($tamano_alto<$questions_numbers_p){

                        $diff_ap = $questions_numbers_p - $tamano_alto;
                        //LLENA EL ARRAY CON PREGUNTAS DE ALTO
                        if($tamano_alto==1){
                            $total_questions[]=$questions_alto[0];
                          }
                          else{
                          $key = array_rand($questions_alto, $tamano_alto);
                            foreach($key as $paso){
                              $total_questions[]=$questions_alto[$paso];
                            }  
                          }
                        
                        if($tamano_medio<$diff_ap){
                          $diff_mp = $diff_ap - $tamano_medio;
                            //LLENA ELK ARRAY CON PREGUNTAS EN MEDIO
                            if($tamano_medio==1){
                              $total_questions[]=$questions_medio[0];
                            }
                            else{
                              $key_m = array_rand($questions_medio, $tamano_medio);
                              if($diff_ap == 1){            
                                $total_questions[]=$questions_medio[$key_m];
                              }else{            
                                foreach($key_m as $paso){
                                  $total_questions[]=$questions_medio[$paso];
                                }
                              }
                            }

                          if($tamano_bajo<$diff_mp){
                            return[];
                          }else{
                            //LLENA EL ARRAY CON PREGUNTAS EN BAJO              
                              if($tamano_bajo==1){
                                $total_questions[]=$questions_bajo[0];
                              }
                              else{
                                $key_b = array_rand($questions_bajo, $diff_mp);
                                if($diff_mp == 1){            
                                  $total_questions[]=$questions_bajo[$key_b];
                                }else{            
                                  foreach($key_b as $paso){
                                    $total_questions[]=$questions_bajo[$paso];
                                  }
                                }
                              }
                            }         

                          }else{
                          //LLENA EL ARRAY DE SALIDA CON PREGUNTAS EN MEDIO SI NO REQUIERE DE PREGUNTAS EN BAJO
                            if($tamano_medio==1){
                              $total_questions[]=$questions_medio[0];
                            }
                            else{
                              $key_m = array_rand($questions_medio, $diff_ap);
                              if($diff_ap == 1){            
                                $total_questions[]=$questions_medio[$key_m];
                              }else{            
                                foreach($key_m as $paso){
                                  $total_questions[]=$questions_medio[$paso];
                                }
                              }
                            }
                          }       
                      }else{
                        //LLENA EL ARRAY DE SALIDA CON SOLO PREGUNTAS EN ALTO SI NO REQUIERE PREGUNTAS NI EN MEDIO NI EN BAJO
                        if($tamano_alto==1){
                          $total_questions[]=$questions_alto[0];
                        }
                        else{
                          $key = array_rand($questions_alto, $questions_numbers_p);
                          foreach($key as $paso){
                            $total_questions[]=$questions_alto[$paso];
                          }  
                        }
                              
                      }

                      //TERMINA CONDICIONALES PARA PREGUNTAS TIPO P

                      }
                      elseif($clasificacion=='TC'){
                        $storage = \Drupal::entityTypeManager()->getStorage('quiz_question');
                        $query = $storage->getQuery();
                        $ids = $query->execute();
                        $questions = !empty($ids) ? $storage->loadMultiple($ids):[];
                        //BARRE PREGUNTA POR PREGUNTA
                        foreach($questions as $quiz_question){
                          $field_clase = $quiz_question->get('field_clasificacion')->entity;
                          $clase = $field_clase ? $field_clase->get('field_id_estatico_terms')[0]->getString() : NULL;
                          ///TIPO DE PREGUNTA
                          $field_tipo_q = $quiz_question ? $quiz_question->get('field_tipo')->referencedEntities()[0]->id() : NULL;
                          //TIPO DE FORMULARIO
                          $field_tipo_f = $quizzes ? $quizzes->get('field_tipo')->referencedEntities()[0]->id():NULL;
                          //REVISA SI LA CLASE DE LAS PREGUNTAS   
                          if($clase=='TC'&& $field_tipo_q == $field_tipo_f){
                            dpm('Ingresa TC');
                            $questions_tc[] = [
                              'qqid'=>$quiz_question->get('qqid')->value,
                              'tid'=>$tid,
                              'type'=>$quiz_question->bundle(),
                              'vid'=>$quiz_question->getRevisionId()
                            ];
                          }            
                        }
                        $questions_numbers_p = $term->get('quiz_question_number')[0]->value;
                        $key= array_rand($questions_tc, $questions_numbers_p);
                        if($questions_numbers_p==1){
                          $total_questions[]=$questions_tc[$key];
                        }else{
                          foreach($key as $paso){
                            $total_questions[]=$questions_tc[$paso];
                          }
                        }
                      }
                      elseif($clasificacion=='TMP'){

                        $storage = \Drupal::entityTypeManager()->getStorage('quiz_question');
                        $query = $storage->getQuery();
                        $ids = $query->execute();
                        $questions = !empty($ids) ? $storage->loadMultiple($ids):[];
                        //BARRE PREGUNTA POR PREGUNTA
                        foreach($questions as $quiz_question){
                          $field_clase = $quiz_question->get('field_clasificacion')->entity;
                          $clase = $field_clase ? $field_clase->get('field_id_estatico_terms')[0]->getString() : NULL;
                          ///TIPO DE PREGUNTA
                          $field_tipo_q = $quiz_question ? $quiz_question->get('field_tipo')->referencedEntities()[0]->id() : NULL;
                          //TIPO DE FORMULARIO
                          $field_tipo_f = $quizzes ? $quizzes->get('field_tipo')->referencedEntities()[0]->id():NULL;
                          //REVISA SI LA CLASE DE LAS PREGUNTAS   
                          if($clase=='TMP' && $field_tipo_q == $field_tipo_f){
                            dpm('Ingresa TMP');
                            $questions_tmp[] = [
                              'qqid'=>$quiz_question->get('qqid')->value,
                              'tid'=>$tid,
                              'type'=>$quiz_question->bundle(),
                              'vid'=>$quiz_question->getRevisionId()
                            ];
                          }            
                        }
                        $questions_numbers_p = $term->get('quiz_question_number')[0]->value;
                        $key= array_rand($questions_tmp, $questions_numbers_p);
                        if($questions_numbers_p==1){
                          $total_questions[]=$questions_tmp[$key];
                        }else{
                          foreach($key as $paso){
                            $total_questions[]=$questions_tmp[$paso];
                          }
                        }
                        
                      }                      
                    }
                  }                 
                  //return $total_questions;
              }
            }
          }
          
        }      
      }   
    }
    dpm($total_questions);
    return ['#markup' => 'Ruta que consulta entidades'];
  }


}
