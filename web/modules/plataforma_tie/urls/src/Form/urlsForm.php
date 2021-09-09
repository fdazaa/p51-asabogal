<?php

namespace Drupal\urlsForm\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
/**
 * Class urlsForm
 */
class urlsForm extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function  getFormId()
  {
    // TODO: Implement getFormId() method.
    return 'module_urls_Form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['user_mail'] = [
      '#type' => 'email',
      '#title' => t('Email ID:'),
      '#required' => TRUE,
    ];
    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Subscribe'),
    ];
    return $form;
  }
  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Nothing.
  }
  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    drupal_set_message($this->t('@user_email ,Your email-id has been sent !', ['@user_email' => $form_state->getValue('user_mail')]));}
}
