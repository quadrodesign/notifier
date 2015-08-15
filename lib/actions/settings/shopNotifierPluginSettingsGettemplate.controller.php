<?php

class shopNotifierPluginSettingsGettemplateController extends waJsonController
{
    public function execute()
    {  
        $id = waRequest::post('id');
        if(is_numeric($id)) {
            $modelNotifierTemplate = new shopNotifierTemplateModel();
            $path = shopNotifierPlugin::path($id);
            $result = $modelNotifierTemplate->getById($id);
            $result['content'] = file_get_contents($path);
            $this->response['result'] = $result; 
            $this->response['message'] = 'ok'; 
        } else {
            $this->response['message'] = 'fail';
        }
    }
}