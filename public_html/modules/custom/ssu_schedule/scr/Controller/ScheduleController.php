<?php

namespace Drupal\ssu_schedule\Controller;
use Drupal\Core\Controller\ControllerBase;


class ScheduleController extends ControllerBase {
  /**
   * Schedule
   *
   * @return array
   *   Our message.
   */

  public function ssu_schedule() {
    return [
      '#markup' => $this->t('Hello World'),
    ];
  }

}

