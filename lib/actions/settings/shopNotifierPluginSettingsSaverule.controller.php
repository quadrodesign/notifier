<?php

class shopNotifierPluginSettingsSaveruleController extends waJsonController
{
  public function execute()
  {
    $data = waRequest::post('settings');
    unset($data['search_name']);
    if (isset($data['data_contact']) && is_array($data['data_contact'])) {
      if ($data['config_name'] != '') {
        if ($data['from'] != '') {
          $info = $data;
          $modelNotifierRule = new shopNotifierRuleModel();
          $data_contact = json_encode($info['data_contact']);
          unset($info['data_contact']);
          $info['data_contact'] = $data_contact;

          $state_name = json_encode($info['state_name']);
          unset($info['state_name']);
          $info['state_name'] = $state_name;

          $info['group_senders'] = ifset($info['group_senders'], 0);
          $info['save_to_order_log'] = ifset($info['save_to_order_log'], 0);

          $result = $modelNotifierRule->getByField('config_name', $data['config_name']);

          $val_update = '';
          $column_insert = '';
          $value_insert = '';
          $len = count($info);
          $i = 0;
          foreach ($info as $key => $value) {
            $i++;
            if ($len == $i) {
              $value_insert .= "'" . $value . "'";
              $column_insert .= $key;
              $val_update .= $key . "='" . $value . "'";
            } else {
              $value_insert .= "'" . $value . "', ";
              $column_insert .= $key . ', ';
              $val_update .= $key . "='" . $value . "',";
            }
          }

          if ($result) {
            $modelNotifierRule->updateById($result['id'], $info);
//                        query("UPDATE shop_notifier_rule SET ".$val_update." WHERE id = '".$result['id']."'");
            $data['id'] = $result['id'];
          } else {
            $data['id'] = $modelNotifierRule->insert($info);
            //query("INSERT INTO shop_notifier_rule (".$column_insert.") VALUES (".$value_insert.")");
          }

          $this->response['data'] = $data;
          $this->response['message'] = 'ok';
        } else {
          $this->response['message'] = 'fail_send_email';
        }
      } else {
        $this->response['message'] = 'fail_config_name_null';
      }
    } else {
      $this->response['message'] = 'fail_data_contact';
    }
  }
}