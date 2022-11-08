<?php

namespace Drupal\example_csv_parse\Controller;
use Drupal\Core\Controller\ControllerBase;


class PageParseCsvController extends ControllerBase {
  /**
   * Schedule
   *
   * @return array
   *   Our message.
   */

  public function myPage() {

    dpm('123');

    $module_handler = \Drupal::service('module_handler');
    $module_path = $module_handler->getModule('example_csv_parse')->getPath();

    dpm($module_path);
    $array = $fields = [];
    $i = 0;
//    $handle = @fopen($module_path . '/files/countries.csv', "r");
    $handle = @fopen($module_path . '/files/ssu_department.csv', "r");




    if ($handle) {
      while (($row = fgetcsv($handle, 4096)) !== FALSE) {
        if (empty($fields)) {
          $fields = $row;
          continue;
        }
        foreach ($row as $k => $value) {
          $array[$i][$fields[$k]] = $value;
        }
        $i++;
      }
      if (!feof($handle)) {
        echo "Error: unexpected fgets() fail\n";
      }
      fclose($handle);
    }

    if (is_array($array) && count($array) > 0) {

      dpm($array);

//      foreach ($array as $city) {
//        $new_city = CityList::create([
//          'id' => $city['id'],
//          'name' => $city['name'],
//          'state_id' => $city['state_id'],
//        ]);
//        $new_city->save();
//      }
    }



    return [
      '#markup' => $this->t('Hello World'),
    ];

  }

}

