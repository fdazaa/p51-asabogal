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

    $storage =\Drupal::entityTypeManager()->getStorage('group');
    $query = $storage->getQuery();
    $ids =$query->execute();
    $groups = !empty($ids) ? $storage->loadMultiple($ids):[];

    foreach ($groups as $group){
      $members = $group->getMembers();
      foreach ($members as $member){
        $id_members = $member->getUser();
        if ($id_members){$id_member = $id_members->id();}
        $id_general = \Drupal::currentUser()->id();
        if ($id_member == $id_general){
          $roles = $member->getRoles();
          foreach ($roles as $rol){
            if($rol){$rol_member = $rol->label();}
            if($rol_member != 'Member'){$rm = $rol_member;}
            //dpm($rm);
          }
        }
      }
    }

    $storage =\Drupal::entityTypeManager()->getStorage('quiz_result_answer');
    $query = $storage->getQuery();
    $ids = $query ->execute();
    $quizzes = !empty($ids) ? $storage ->loadMultiple($ids):[];
    foreach ($quizzes as $quiz){

      $score = $quiz;
      dpm($score);
    }



    //$this->adopcionCreateIndicadores($rm);





    return ['#markup' => 'Ruta que crear entidades'];
  }


  public function adopcionCreateIndicadores($title){
    $values = [
      'title' => $title,
      'proceso_1'=> 3,
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
