<?php

class shopStatusnotifierLogModel extends waModel
{
    protected $table = 'shop_statusnotifier_log';

    public function add($data)
    {
        $data['date'] = date('Y-m-d H:i:s');
        $log_id = $this->insert($data);
        return $log_id;
    }
    
    public function getLastDateByConfigId($id) {
        return $this->select('date')
                      ->where('config_id = '.(int)$id)
                      ->order('datetime DESC')
                      ->fetchField();
    }
}
