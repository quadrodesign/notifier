<?php

class shopNotifierPluginSettingsGetthemeController extends waJsonController
{
    public function execute()
    {  
        $id = waRequest::post('id');
        if(is_numeric($id)) {
            $model = new waModel();
            $path = wa()->getDataPath('plugins/notifier/templates/' . $id . '.html', false, 'shop', true);
            $result = $model->query("SELECT * FROM shop_notifier_template WHERE id = '".$id."'")->fetchAssoc();
            $result['content'] = file_get_contents($path);
            $this->response['result'] = $result; 
            $this->response['message'] = 'ok'; 
        } else {
            $this->response['message'] = 'fail';
        }
    }
}