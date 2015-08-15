<?php

class shopStatusnotifierPluginSettingsDeletenotificationController extends waJsonController
{
    public function execute()
    {
        $id = waRequest::post('id');
        if(is_numeric($id)) {
            $model = new waModel();
            $model->query("DELETE FROM shop_statusnotifier_configuration WHERE id = '".$id."'");
            $this->response['message'] = 'ok';
        } else {
            $this->response['message'] = 'fail';
        }
    }
}