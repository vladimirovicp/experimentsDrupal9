<?php

  namespace Drupal\ssu_schedule\Controller;
  use Drupal\Core\Controller\ControllerBase;
  /**
  * Controller for the salutation message.
  */

  class HelloWorldController extends ControllerBase {
      /**
      * Hello World.
      *
      * @return array
      *   Our message.
      */

      public function helloWorld() {
      return [
      '#markup' => $this->t('Hello World'),
      ];
    }

  }
