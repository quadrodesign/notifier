<?php

class shopNotifierPluginSettingsSavetemplateController extends waJsonController
{
    public function execute()
    {
        $id = waRequest::post('id');
        $name = waRequest::post('name');
        $content = waRequest::post('content');
        $model = new waModel();
        
        if(is_numeric($id)){
            $model->query("UPDATE shop_notifier_template SET name = '".$name."' WHERE id = '".$id."'");
        } else {
            $result = $model->query("INSERT INTO shop_notifier_template (name) VALUES ('".$name."')");
            $id = $result->lastInsertId();
        }
        $template_path = shopNotifierPlugin::path($id);
        $f = fopen($template_path, 'w');
        if (!$f) {
            throw new waException('Не удаётся сохранить шаблон. Проверьте права на запись ' . $template_path);
        }
        fwrite($f, $content);
        fclose($f);

        $this->response['result'] = array('id' => $id, 'name' => $name);
    }
}