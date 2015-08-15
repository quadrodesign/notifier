<?php

class shopNotifierPluginSettingsDeletetemplateController extends waJsonController
{
    public function execute()
    {
        $id = waRequest::post('id');
        if(is_numeric($id)) {
            $modelNotifierTemplate = new shopNotifierTemplateModel();
            $path = shopNotifierPlugin::path($id);
            waFiles::delete($path);
            $modelNotifierTemplate->deleteById($id);
            $this->response['message'] = 'ok';
        } else {
            $this->response['message'] = 'fail';
        }
    }
}