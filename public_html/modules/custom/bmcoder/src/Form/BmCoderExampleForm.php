<?php

namespace Drupal\bmcoder\Form;

//use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;


class BmCoderExampleForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'bmcoder_form_add';
  }

  /**
   * {@inheritdoc}
   */
  //Данный метод отвечает за саму форму, то что в ней будет. Возвращает массив с render arrays.
  public function buildForm(array $form, FormStateInterface $form_state, $arg = NULL) {

    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#default_value' => $arg,
      '#size' => 25,
    ];


    $form['actions']['#type'] = 'action';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
      '#button_type' => 'primary',
    ];
    return $form;
  }


  /**
   * {@inheritdoc}
   */
  //submitForm():
  //обработчик, который вызывается при отправке формы (если проверка прошла без ошибок).
  //Он получает те же аргументы, что и validateForm().
  public function submitForm(array &$form, FormStateInterface $form_state)
  {

//    \Drupal::messenger()
//      ->addMessage('Сообщение успешно отправлено example="' . $form_state->getValue('example'), 'status');
//
//    //$form_state->setRedirect('<front>');

    $name = $form_state->getValue('name');
     $query = \Drupal::database();
     $query->insert('bmcoder_user')->fields(array(
       'name' => $name
     )) ->execute();

     \Drupal::messenger()->addMessage('Data Saved');


  }

}

