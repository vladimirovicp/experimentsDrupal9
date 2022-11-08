<?php
namespace Drupal\ssu_schedule\Controller;
use Drupal\Core\Controller\ControllerBase;


class DepartmentShowController extends ControllerBase {
  /**
   * Schedule
   *
   * @return array
   *   Our message.
   */

  public function department_show() {

    # Выборка с сортировкой по полю

//      $query = \Drupal::database()->select('ssu_department', 'nfd');
//      $query->fields('nfd', array('id_department', 'ssu_department'));
//      $query->condition('nfd.id_department', 1);
//
//      dpm($query);

//    $query = \Drupal::database()->select('ssu_department', 'de');
//    $query->fields('de', array('id_department', 'ssu_department'));
//    $query->orderBy('de.id_department', 'DESC');
//    $query->range(0, 1);

    $output = \Drupal::database()
      ->select('ssu_department', 'n')
      ->fields('n', ['id_department', 'department'])
//      ->condition('n.type', 'page') // <--
      ->condition('n.id_department', 3822)
      ->execute()
      ->fetchAll();





   dpm($output);

//      $query->orderBy('de.id_department', 'DESC');


//      $department = $query->execute()->fetchAll();
//
////    return $output;
//
//      dpm($department);


//    return $mail;



    return [
      '#markup' => $this->t('123456'),
    ];

  }

}

