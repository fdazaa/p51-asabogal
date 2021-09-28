<?php

namespace  Drupal\checklist_task\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;

class checklistForm extends FormBase
{
  /**
   * {@inheritDoc}
   */
  public function getFormId()
  {
    // TODO: Implement getFormId() method.
    return 'chechlist_t5ask';
  }


  public function buildForm(array $form, FormStateInterface $form_state)
  {
    // TODO: Implement buildForm() method.

   // $this->ver();

    $opciones = $this->extraertareas(80);

    $form['actividades_ch'] = [
      '#type'=>'checkboxes',
      '#title'=> $this->t('Actividades'),
      '#options'=> $opciones[1],
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('submit'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */

  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    // TODO: Implement submitForm() method.

    /*$node = Node::create(array(
      'type' => 'actividades',
      'title' => 'tarea',
      'langcode' => 'en',
      'uid' => '1',
      'status' => 1,
      'field_text'=>strval($form_state->getValue('actividades_ch')),
    ));

    $node->save();*/
  }



  function ver(){
    $storage = \Drupal::entityTypeManager()->getStorage('node');
    $query = $storage->getQuery()
      ->condition('type','actividades');
    $ids = $query->execute();
    $nodes = !empty($ids) ? $storage->loadMultiple($ids):NULL;
    foreach ($nodes as $node){
      //dpm($node->get('field_text'));
    }

  }


  function extraertareas($gid){
    $group = \Drupal::entityTypeManager()->getStorage('group')->load($gid);
    $title = $group->label();
    $field_tareas = $group->get('field_tareas')->getValue();
    $log = sizeof($field_tareas);
    for ($i=0;$i<$log;$i++){
      $opciones[$i]= $this->t(strval($field_tareas[$i]['value']));
    }
    $field[0]=$title;
    $field[1]=$opciones;

    return $field;

  }


}
