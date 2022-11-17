<?php
namespace Drupal\ssu_schedule\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Drupal\Core\Url;

class ScheduleShowController extends ControllerBase {

  public function schedule_show($faculty,$form_education,$group) {

//    dpm($faculty);
//    dpm($form_education);
//    dpm($group);

    $edu = match ($form_education) {
      "zo" => 1,
      "vo" => 2,
      default => 0,
    };

    $pager = false;

    $alias = (strlen($faculty)<=5)?htmlspecialchars($faculty, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5):"";
    $number = htmlspecialchars($group, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5);

//    dpm($alias);
//    dpm($number);


    $info = \Drupal::database()->query("SELECT *
                            FROM ssu_group g, (SELECT * FROM ssu_department d WHERE d.alias = :alias )d
                            WHERE d.id_department = g.id_department  AND form_education = :edu AND number = :number",
    [
      ':alias' => $alias,
      ':edu' => $edu,
      ':number' => $number,
    ])->fetchAll();

//    dpm($info);

    if(empty($info)){
      $response = new TrustedRedirectResponse(
        Url::fromUri('internal:/schedule')->toString()
      );
      $response->send();
    }

    $grid = [];
    $r1 = \Drupal::database()->query("SELECT * FROM {ssu_schedule_grid} WHERE id_type=:id_type ORDER BY lesson", array(':id_type' => $info[0]->id_type_grid));

//    dpm($r1);

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

//    if(!empty($_POST['js']))
//      exit(schedule_json($cells,1));

    $archive = array();
    $r = \Drupal::database()->query("SELECT * FROM ssu_schedule_archive ORDER BY archive DESC");
    while($row = $r->fetchAssoc()){
      $archive[] = array(
        'id' => $row['id_archive'],
        'name' => $row['archive']
      );
    }

    $last_update = "";
    foreach($cells as $c){
      if(date($last_update) <= date($c['update'])){
        $last_update = $c['update'];
      }
    }

    dpm($last_update);
















    return [
      '#markup' => $this->t('Hello World'),
    ];

  }

//  public function titleCallback($param) {
//    return  $param;
//  }


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
WHERE id_group = 211 AND id_archive = 0 ORDER BY `state`, `date_event`, `day_of_week`, `date_begin`;");

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

/**
 * Формирует расписание в форме json
 * cells - массив занятий
 * type_grid - тип сетки расписания
 * js - если 0, то возвращает строку с переменными при загрузке страницы
 *      если 1, то возвращает json для ajax запроса
 */

//function schedule_json($cells,$js){
//  $types = "";
//  $lesson_types = '';
//  $r = db_query("SELECT * FROM {ssu_schedule_event_type} ORDER BY id_type DESC");
//  $types .= "{";
//  $lesson_types .= "{";
//  $i = -1; $j = -1;
//  while($row = $r->fetchAssoc()){
//    if($row['type_full'] != 'Консультация' && $row['type_full'] != 'Зачет' && $row['type_full'] != 'Экзамен'){
//      if ($j != -1)
//        $lesson_types .= ",";
//      $lesson_types .= "'".$row['id_type']."': '".$row['type_full']."'";
//      $j++;
//    }
//    if($i != -1)
//      $types .= ",";
//    $types .=  "'".$row['id_type']."': '".$row['type_full']."'";
//    $i++;
//  }
//  $types .= "}";
//  $lesson_types .= "}";
//  $json = "[";
//  $i=-1;
//  foreach($cells as $cell){
//    if($i != -1)
//      $json .= ",";
//    $json .= "{
//      \"id\":\"{$cell['id']}\",
//      \"dow\":{$cell['dow']},
//      \"state\":\"{$cell['state']}\",
//      \"description\":\"{$cell['description']}\",
//      \"place\":\"{$cell['place']}\",
//      \"id_disc\":\"{$cell['id_disc']}\",
//      \"discipline\":\"".addslashes($cell['discipline'])."\",
//      \"id_teacher\": \"{$cell['id_teacher']}\",
//      \"lesson\":{$cell['lesson']},
//      \"ltype\":\"{$cell['lesson_type']}\",
//      \"date_begin\":\"{$cell['date_begin']}\",
//      \"date_end\":\"{$cell['date_end']}\"
//    }";
//    $i++;
//  }
//  $json .="]";
//  if($js === 0){
//    $output = "
//      <script>
//        var dow = 'empty';
//        var ltype = {$lesson_types};
//        var types = {$types};
//        var lessons = {$json};
//      </script>";
//    return $output;
//  }
//  else
//    print $json;
//}
