<?php

namespace Drupal\checklist_taskForm\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;


class checklist_taskForm extends FormBase {
  /**
   * {@inheritdoc}
   */

  public function getFormId()
  {
    // TODO: Implement getFormId() method.
    return 'checklist_taskForm';
  }

  public function buildForm(array $form, FormStateInterface $form_state)
  {
    // TODO: Implement buildForm() method.
    $form['Actividades'] = [
      '#type'=>'checkboxes',
      '#title' => $this->t('Actividades'),
      '#options' => array(
        [
          'activity1'=>'activity 1',
          'activity2'=>'activity 2'
          ]
      )
    ];

    $form['actions']['#type']='actions';

    $form['actions']['submit'] = [
      '#type'=>'submit',
      '#value'=> $this->t('Submit'),
      '#button_type' => 'primary',
    ];

    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    // TODO: Implement submitForm() method.
    $this->messenger()->addStatus($this->t('Sus tareas an sido actualizadas'));

  }


}
