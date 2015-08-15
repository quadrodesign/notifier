<?php

class shopNotifierPluginSettingsDeleteruleController extends waJsonController
{
    public function execute()
    {
        $id = waRequest::post('id');
        if(is_numeric($id)) {
            $modelNotifierConfig = new shopNotifierConfigModel();
            $modelNotifierConfig->deleteById($id);
            $this->response['message'] = 'ok';
        } else {
            $this->response['message'] = 'fail';
        }
    }
}