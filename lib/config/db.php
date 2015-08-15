<?php
return array(
  'shop_notifier_config' => array(
    'id' => array('int', 11, 'null' => 0, 'autoincrement' => 1),
    'config_name' => array('varchar', 255, 'null' => ''),
    'data_contact' => array('text', 'null' => 0),
    'state_name' => array('text', 'null' => 0),
    'number_time' => array('int', 2, 'null' => 0),
    'period' => array('varchar', 3, 'null' => 'd'),
    'from' => array('varchar', 255, 'null' => ''),
    'theme' => array('int', 5, 'null' => 0),
    'group_senders' => array('int', 1, 'null' => 0),
    'save_to_order_log' => array('int', 1, 'null' => 0),
    'repeat_number_time' => array('int', 11, 'null' => 0),
    'repeat_period' => array('varchar', 3, 'null' => 'm'),
    ':keys' => array(
      'PRIMARY' => 'id'),
  ),
  'shop_notifier_template' => array(
    'id' => array('int', 11, 'null' => 0, 'autoincrement' => 1),
    'name' => array('varchar', 255, 'null' => ''),
    ':keys' => array(
      'PRIMARY' => 'id'),
  ),
  'shop_notifier_log' => array(
    'id' => array('int', 11, 'null' => 0, 'autoincrement' => 1),
    'config_id' => array('int', 11, 'null' => 0),
    'create_datetime' => array('datetime', 'null' => 0),
    ':keys' => array(
      'PRIMARY' => 'id'),
  ),
);