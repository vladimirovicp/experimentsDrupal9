<?php
namespace Drupal\ssu_schedule\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Drupal\Core\Url;

use Drupal\ssu_schedule\ScheduleAncillary;

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


//    $type_short_full = [];
//
//    for ($i=0; $i< count())

//    dpm($last_update);



//    dpm(ScheduleAncillary::dayOfWeek('1'));

    $day_of_week = [];
    for($i=1;$i<=6;$i++){
      $day_of_week[$i] =  ScheduleAncillary::dayOfWeek($i);
    }

//    return [
//      '#markup' => $this->t('Hello World'),
//    ];

//    dpm($cells);

    $types = ScheduleAncillary::schedule_lesson_full_type();

    $schedule_table= [];


    $descriptions = array();
    $output = '';
    $z = 0;
    foreach($grid as $gr){
//      $output .= "<tr>";
//      $output .= "<th>".substr($gr['begin'],0,5)."<br>".substr($gr['end'],0,5)."</th>";
      $schedule_table[$z]['time_begin'] = substr($gr['begin'],0,5);
      $schedule_table[$z]['time_end'] = substr($gr['end'],0,5);


//      dpm($cells);


      for($i=1;$i<=6;$i++){
        $lesson = ""; $bcgrw = '';
        foreach($cells as $cell ){
          /* ---==========================================================---
            Костыль для решения проблем:
                    Notice: Undefined variable: t в функции make_schedule_table() (строка 942 в файле /www/webdev.sgu.ru/html/sites/all/modules/ssu/ssu_schedule/ssu_schedule.module).
                    Notice: Undefined index: update в функции make_schedule_table() (строка 940 в файле /www/webdev.sgu.ru/html/sites/all/modules/ssu/ssu_schedule/ssu_schedule.module).
          */
          if(empty($cell['update'])){
            $t = 0;
          }else{
            $t = mktime(0,0,0, substr($cell['update'],5,2), substr($cell['update'],8,2), substr($cell['update'],0,4));
          }
          /* ---==========================================================--- */

          if( $gr['lesson'] == $cell['lesson'] && $i == $cell['dow']){
            $l = '';
            $pr = '';

            if(time()-$t <24*60*60)
              $bcgrw = "greentd";
            $r = '';

            $schedule_table[$z]['lesson'][$i]['state'] = $cell['state'];

            switch($cell['state']){
              case '2': $r .= "чис."; break;
              case '3': $r .= "знам."; break;
              default: break;
            }
//            $pr .= "<div class='l-pr-r' title='Чётность'>{$r}</div>";
//            $pr .= "<div class='l-pr-t' title='Тип'>"
//              .  '0'
////              schedule_lesson_type($cell['lesson_type'])
//              ."</div>";

            $schedule_table[$z]['lesson'][$i]['lesson_type']=$cell['lesson_type'];
            $schedule_table[$z]['lesson'][$i]['description']=$cell['description'];

//            $pr .= "<div class='l-pr-g' alt='Другое'>{$cell['description']}</div>";
//            $l .= "<div class='l-pr'>{$pr}</div>";

//            $l .= "<div class='l-dn'>{$cell['discipline']}</div>";

            $schedule_table[$z]['lesson'][$i]['discipline']=$cell['discipline'];



            if (!empty($cell['teacher_nid'])) {
//              $l .= "<div class='l-tn'>" .
//                '0'
////                l($cell['teacher'], 'node/'.$cell['teacher_nid'])
//                . "</div>";
              $schedule_table[$z]['lesson'][$i]['teacher_nid'] = $cell['teacher_nid'];
            }else{
//              $l .= "<div class='l-tn'>{$cell['teacher']}</div>";
              $schedule_table[$z]['lesson'][$i]['teacher_nid'] = $cell['teacher_nid'];
            }
            $l .= "<div class='l-p'>{$cell['place']}</div>";

//            if($schedule_type == 'teacher'){
//              $l .= "<div class='l-g'>{$cell['number']}гр. {$cell['form']}</div>";
//            }

            if ( $cell['date_begin'] !='null' && $cell['date_end']!='null' &&  !is_null($cell['date_begin']) && !is_null($cell['date_end'])  ){
              $l .=
                '<div class="l-d">' .
                preg_replace('/(\d+)-(\d+)-(\d+)/','$3.$2.$1', $cell['date_begin']) .
                "&nbsp;&ndash;&nbsp;" .
                preg_replace('/(\d+)-(\d+)-(\d+)/','$3.$2.$1', $cell['date_end']) .
                '</div>';
            }
            $g = array_search($cell['description'], $descriptions);
            if($g === false){
              $descriptions[] = $cell['description'];
              end($descriptions);
              $g = key($descriptions);
            }
            $lesson .= "<div class='l l--t-{$cell['lesson_type']} l--r-{$cell['state']} l--g-{$g}'>{$l}</div>";
          }
        }

        $output .= "<td  id='{$gr['lesson']}_{$i}' class='{$bcgrw}'>{$lesson}</td>";

      }
      $output .= "</tr>";

      $z++;
    }

//    dpm($output);

//    dpm($schedule_table);


    return array(
      '#theme' => 'schedule_table',
      '#dayOfWeek' =>  $day_of_week,
      '#grid' => $grid,
      '#cells' => $cells,
      '#types' => $types,
    );

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
