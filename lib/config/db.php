<?php
return array(
  'shop_statusnotifier_configuration' => array(
    'id' => array('int', 11, 'null' => 0, 'autoincrement' => 1),
    'config_name' => array('varchar', 255, 'null' => ''),
    'data_contact' => array('text', 'null' => 0),
    'state_name' => array('text', 'null' => 0),
    'number_time' => array('int', 2, 'null' => 0),
    'period' => array('varchar', 3, 'null' => 'd'),
    'send_email' => array('varchar', 255, 'null' => ''),
    'theme' => array('int', 5, 'null' => 0),
    ':keys' => array(
      'PRIMARY' => 'id'),
  ),
  'shop_statusnotifier_shablon' => array(
    'id' => array('int', 11, 'null' => 0, 'autoincrement' => 1),
    'name' => array('varchar', 255, 'null' => ''),
    ':keys' => array(
      'PRIMARY' => 'id'),
  ),
);