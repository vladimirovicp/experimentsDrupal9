<?php

//ScheduleAncillary.php

namespace Drupal\ssu_schedule;

/**
 * Provides my special service.
 */
class ScheduleAncillary{

  /**
   * По номеру дня возвращает его полное название
   */
  public static function dayOfWeek($day){
    switch($day){
      case '1': return "Понедельник";
      case '2': return "Вторник";
      case '3': return "Среда";
      case '4': return "Четверг";
      case '5': return "Пятница";
      case '6': return "Суббота";
      case '7': return "Воскресенье";
    }
  }

  /**
   * По типу занятия из БД возвращает его тип для отображения на странице
   */
  public static function schedule_lesson_type($type){
    $query = \Drupal::database()->select('ssu_schedule_event_type', 'd');
    $query -> condition('d.id_type', $type);
    $query->fields('d', array('type_short'));
    return $query->execute()->fetchAll();
  }

  public static function schedule_lesson_full_type(){
    $query = \Drupal::database()->select('ssu_schedule_event_type', 'd');
    $query->fields('d', array('type_short'));
    $query->fields('d', array('id_type'));
    $types = $query->execute()->fetchAll();

    $result = [];
    for ($i = 0; $i < count($types); $i++){
      $result[$types[$i]->id_type] = $types[$i]->type_short;
    }

    return $result;
  }
}
