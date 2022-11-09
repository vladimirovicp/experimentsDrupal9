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
    $query = \Drupal::database()->select('ssu_department', 'de');
    $query->fields('de', array('id_department', 'department','short_name','alias','id_type_grid','section'));
    $query->condition('de.section', 0);
    $query->orderBy('de.department', 'ASC');
    $faculties = $query->execute()->fetchAll();

    $query = \Drupal::database()->select('ssu_department', 'de');
    $query->fields('de', array('id_department', 'department','short_name','alias','id_type_grid','section'));
    $query->condition('de.section', 3);
    $query->orderBy('de.department', 'ASC');
    $college = $query->execute()->fetchAll();



//    return [
//      '#markup' => $this->t('123456'),
//    ];

    return array(
      '#theme' => 'schedule_page',
      '#faculties' => $faculties,
      '#college' => $college,
    );

  }

}

