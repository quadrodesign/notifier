<?php

class shopStatusnotifierPluginSettingsSaveController extends waJsonController
{
    public function execute()
    {
        $data = waRequest::post('settings');
        unset($data['search_name']);
        if(isset($data['data_contact']) && is_array($data['data_contact'])) {
            if($data['config_name'] != '') {
                if($data['send_email'] != '') {
                    $info = $data;
                    $model = new waModel();
                    $data_contact = json_encode($info['data_contact']);
                    unset($info['data_contact']);
                    $info['data_contact'] = $data_contact;
                    
                    $state_name = json_encode($info['state_name']);
                    unset($info['state_name']);
                    $info['state_name'] = $state_name;
                    
                    $result = $model->query("SELECT id FROM shop_statusnotifier_configuration WHERE config_name = '".mysql_escape_string($data['config_name'])."'")->fetchField();
                    
                    $val_update = '';
                    $column_insert = '';
                    $value_insert = '';
                    $len = count($info);
                    $i = 0;
                    foreach($info as $key => $value) {
                        $i++;
                        if($len == $i) {
                            $value_insert .= "'".$value."'";
                            $column_insert .= $key;
                            $val_update .= $key."='".$value."'";
                        } else {
                            $value_insert .= "'".$value."', ";
                            $column_insert .= $key.', ';
                            $val_update .= $key."='".$value."',";
                        } 
                    }
                    
                    if($result) {
                        $model->query("UPDATE shop_statusnotifier_configuration SET ".$val_update." WHERE id = '".$result."'");
                        $data['id'] = $result;
                    } else {
                        $result = $model->query("INSERT INTO shop_statusnotifier_configuration (".$column_insert.") VALUES (".$value_insert.")");
                        $data['id'] = $result->lastInsertId();
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