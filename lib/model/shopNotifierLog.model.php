<?php

class shopNotifierLogModel extends waModel
{
    protected $table = 'shop_notifier_log';

    public function add($data)
    {
        $data['create_datetime'] = date('Y-m-d H:i:s');
        $log_id = $this->insert($data);
        return $log_id;
    }
    
    public function getLastDateByConfigId($id) {
        return $this->select('create_datetime')
                      ->where('config_id = '.(int)$id)
                      ->order('create_datetime DESC')
                      ->fetchField();
    }
}
