<?php

class shopStatusnotifierPluginSettingsGetthemeController extends waJsonController
{
    public function execute()
    {  
        $id = waRequest::post('id');
        if(is_numeric($id)) {
            $model = new waModel();
            $path = wa()->getDataPath('plugins/receiveemail/templates/actions/frontend/' . $id . '.html', false, 'shop', true);
            $result = $model->query("SELECT * FROM shop_statusnotifier_shablon WHERE id = '".$id."'")->fetchAssoc();
            $result['content'] = file_get_contents($path);
            $this->response['result'] = $result; 
            $this->response['message'] = 'ok'; 
        } else {
            $this->response['message'] = 'fail';
        }
    }
}