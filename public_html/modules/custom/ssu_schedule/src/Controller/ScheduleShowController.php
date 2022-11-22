<?php
namespace Drupal\ssu_schedule\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Drupal\Core\Url;

use Drupal\ssu_schedule\ScheduleAncillary;

class ScheduleShowController extends ControllerBase {

  public function schedule_show($faculty,$form_education,$group) {

    $edu = match ($form_education) {
      "zo" => 1,
      "vo" => 2,
      default => 0,
    };

    $pager = false;

    $alias = (strlen($faculty)<=5)?htmlspecialchars($faculty, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5):"";
    $number = htmlspecialchars($group, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5);

    $info = \Drupal::database()->query("SELECT *
                            FROM ssu_group g, (SELECT * FROM ssu_department d WHERE d.alias = :alias )d
                            WHERE d.id_department = g.id_department  AND form_education = :edu AND number = :number",
    [
      ':alias' => $alias,
      ':edu' => $edu,
      ':number' => $number,
    ])->fetchAll();

    if(empty($info)){
      $response = new TrustedRedirectResponse(
        Url::fromUri('internal:/schedule')->toString()
      );
      $response->send();
    }

    $grid = [];
    $r1 = \Drupal::database()->query("SELECT * FROM {ssu_schedule_grid} WHERE id_type=:id_type ORDER BY lesson", array(':id_type' => $info[0]->id_type_grid));

    while($row = $r1->fetchAssoc()){
      $grid[] = array(
        'lesson' => $row['lesson'],
        'begin' => $row['begin'],
        'end' => $row['end']
      );
    }


    // Нужно дописать когда будут юзеры!!! **************************************************************************
    // $cells = _schedule_get_schedule_data($info[0]->id_group);

    $cells = _schedule_get_schedule_data($info[0]->id_group);

//    $archive = array();
//    $r = \Drupal::database()->query("SELECT * FROM ssu_schedule_archive ORDER BY archive DESC");
//    while($row = $r->fetchAssoc()){
//      $archive[] = array(
//        'id' => $row['id_archive'],
//        'name' => $row['archive']
//      );
//    }

    $last_update = "";
    foreach($cells as $c){
      if(date($last_update) <= date($c['update'])){
        $last_update = $c['update'];
      }
    }

    $day_of_week = [];
    for($i=1;$i<=6;$i++){
      $day_of_week[$i] =  ScheduleAncillary::dayOfWeek($i);
    }
    $types = ScheduleAncillary::schedule_lesson_full_type();


    return array(
      '#theme' => 'schedule_table',
      '#dayOfWeek' =>  $day_of_week,
      '#grid' => $grid,
      '#cells' => $cells,
      '#types' => $types,
    );

  }
}

function _schedule_get_schedule_data($id_group){
  $cells = array();


  /* Когда будут юзеры поправить! ***********************************************************/
//  $r = \Drupal::database()->query("SELECT s.*, `discipline`, concat(UPPER(substring(`surname`,1,1)), LOWER(substring(`surname`,2)), ' ', UPPER(substring(`name`,1,1)), '. ', UPPER(substring(`second_name`,1,1)), '.') as fio, (SELECT i.nid FROM ssu_schedule_ident i WHERE i.tid=IFNULL(t.id_teacher, -1) AND i.nid IN (SELECT nid FROM node n WHERE n.nid=i.nid AND n.`status`=1 AND n.`type`='employee') LIMIT 0,1) as t_nid FROM ssu_schedule s INNER JOIN ssu_discipline d on s.id_discipline = d.id_discipline LEFT JOIN ssu_teacher t on s.id_teacher = t.id_teacher WHERE id_group = :id_group AND id_archive = 0 ORDER BY `state`, `date_event`, `day_of_week`, `date_begin`", array(':id_group' => $id_group));


  $r = \Drupal::database()->query("SELECT s.*, `discipline`,
concat(UPPER(substring(`surname`,1,1)),
  LOWER(substring(`surname`,2)), ' ',
  UPPER(substring(`name`,1,1)), '. ',
  UPPER(substring(`second_name`,1,1)), '.') as fio, (SELECT i.nid FROM ssu_schedule_ident i WHERE i.tid=IFNULL(t.id_teacher, -1) ) as t_nid
FROM ssu_schedule s INNER JOIN ssu_discipline d on
s.id_discipline = d.id_discipline LEFT JOIN ssu_teacher t on
s.id_teacher = t.id_teacher
WHERE id_group = :id_group AND id_archive = 0 ORDER BY `state`, `date_event`, `day_of_week`, `date_begin`;", array(':id_group' => $id_group));

  while($row = $r->fetchAssoc()){
    $el = array(
      'id' => $row['id_schedule'],
      'dow' => $row['day_of_week'],
      'state' => $row['state'],
      'description' => $row['description'],
      'place' => $row['place'],
      'id_disc' => $row['id_discipline'],
      'discipline' => $row['discipline'],
      'id_teacher' => $row['id_teacher'],
      'teacher' => ($row['fio'] != ' ..')?$row['fio']:"",
      'teacher_nid' => $row['t_nid'],
      'lesson' => $row['lesson'],
      'lesson_type' => $row['type'],
      'date' => $row['date_event'],
      'published' => $row['published'],
      //'weight' => $row['weight'],
      'update' => $row['update'],
      'date_begin' => (!$row['date_begin']?"null":$row['date_begin']),
      'date_end' => (!$row['date_end']?"null":$row['date_end']),
    );
    //if ($row['lesson'] == 0) drupal_set_message("<pre>".print_r($el, 1)."</pre>", 'error');
    $cells[] = $el;
  }

  return $cells;
}
