<?php
namespace Drupal\ssu_schedule\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Drupal\Core\Url;


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

    $education_count = '';
    $type_count = '';
    $course_count = '';
    $group_count = '';

    $group_format = [];

    $education_info = [
      0 => [
        'title'=> 'Дневная форма обучения',
        'alias'=> 'do'
      ],
      1 => [
        'title'=> 'Заочная форма обучения',
        'alias'=> 'zo'
      ],
      2 => [
        'title'=> 'Вечерняя форма обучения',
        'alias'=> 'vo'
      ]
    ];

    $education_info_type = [
      0 => 'Специалитет',
      1 => 'Бакалавриат',
      2 => 'Магистратура',
      3 => 'Аспирантура',
    ];


    for ($i = 0; $i< count($group); $i++){
      if($group[$i]->form_education != $education_count){
        $education_count = $group[$i]->form_education;
        $group_format[$education_count]['title'] = $education_info[$education_count]['title'];
        $group_format[$education_count]['alias'] = $education_info[$education_count]['alias'];
        $type_count = '';
      }

      if($group[$i]->grp_type != $type_count){
        $type_count = $group[$i]->grp_type;
        $group_format[$education_count]['info'][$type_count]['title'] = $education_info_type[$type_count];

      }

      if( floor(intval($group[$i]->number)/100)%10 != $course_count){
        $course_count = floor(intval($group[$i]->number)/100)%10;
        $group_format[$education_count]['info'][$type_count]['info'][$course_count]['name'] = $course_count;
        $group_count = 0;
      }
      $group_format[$education_count]['info'][$type_count]['info'][$course_count]['group'][$group_count] = $group[$i]->number;
      $group_count++;

    }

    if (empty($group)){
      $response = new TrustedRedirectResponse(
        Url::fromUri('internal:/schedule')->toString()
      );
      $response->send();
    }

    return array(
      '#theme' => 'schedule_group',
      '#faculty' => $param,
      '#group' => $group_format,
    );

  }


  public function titleCallback($param) {
    return  $param;
  }

}

