<?php

class shopStatusnotifierPluginSettingsDeletethemeController extends waJsonController
{
    public function execute()
    {
        $id = waRequest::post('id');
        if(is_numeric($id)) {
            $model = new waModel();
            $path = wa()->getDataPath('plugins/statusnotifier/templates/actions/frontend/' . $id . '.html', false, 'shop', true);
            waFiles::delete($path);
            $model->query("DELETE FROM shop_statusnotifier_shablon WHERE id = '".$id."'");
            $this->response['message'] = 'ok';
        } else {
            $this->response['message'] = 'fail';
        }
    }
}