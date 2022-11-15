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

    dpm($info);

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

//    dpm($grid);

//    $cells = schedule_get_schedule_data($info['id_group']);









    return [
      '#markup' => $this->t('Hello World'),
    ];

  }

//  public function titleCallback($param) {
//    return  $param;
//  }


}
