<?php

 $settings['trusted_host_patterns'] = [
    '^experimentsdrupal\.local$',
    '^.+\.eexperimentsdrupal\.local$',
 ];

$databases['default']['default'] = array(
  'database' => "xxx",
  'username' => "xxx",
  'password' => "xxx",
  'host' => 'localhost',
  'port' => '3306',
  'namespace' => 'Drupal\\Core\\Database\\Driver\\mysql',
  'driver' => 'mysql',
);
