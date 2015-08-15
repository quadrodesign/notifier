<?php

class shopNotifierPluginSettingsDeletethemeController extends waJsonController
{
    public function execute()
    {
        $id = waRequest::post('id');
        if(is_numeric($id)) {
            $model = new waModel();
            $path = shopNotifierPlugin::path($id);
            waFiles::delete($path);
            $model->query("DELETE FROM shop_notifier_template WHERE id = '".$id."'");
            $this->response['message'] = 'ok';
        } else {
            $this->response['message'] = 'fail';
        }
    }
}