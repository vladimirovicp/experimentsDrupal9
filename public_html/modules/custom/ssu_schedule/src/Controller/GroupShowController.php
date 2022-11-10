<?php
namespace Drupal\ssu_schedule\Controller;
use Drupal\Core\Controller\ControllerBase;


class GroupShowController extends ControllerBase {
  /**
   * Schedule
   *
   * @return array
   *   Our message.
   */

  public function group_show($param) {

    $query = \Drupal::database()->select('ssu_department', 'd');
    $query->leftJoin('ssu_group', 'g', 'g.id_department = d.id_department');
    $query->fields('g', array('form_education', 'grp_type','number'));
    $query -> condition('d.alias', $param);
    $query -> orderBy('form_education','ASC');
    $query -> orderBy('grp_type','ASC');
    $query -> orderBy('number','ASC');
    $group = $query->execute()->fetchAll();

    return array(
      '#theme' => 'schedule_group',
      '#group' => $group,
    );



  }


  public function titleCallback($param) {

    return  $param;

  }

}

