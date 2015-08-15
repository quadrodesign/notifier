<?php

class shopStatusnotifierPluginSettingsSaveshablonController extends waJsonController
{
    public function execute()
    {
        $id = waRequest::post('id');
        $name = waRequest::post('name');
        $content = waRequest::post('content');
        $model = new waModel();
        
        if(is_numeric($id)){
            $model->query("UPDATE shop_statusnotifier_shablon SET name = '".$name."' WHERE id = '".$id."'");
            $template_path = wa()->getDataPath('plugins/receiveemail/templates/actions/frontend/' . $id . '.html', false, 'shop', true);
            $f = fopen($template_path, 'w');
            if (!$f) {
                throw new waException('Не удаётся сохранить шаблон. Проверьте права на запись ' . $template_path);
            }
            fwrite($f, $content);
            fclose($f);
        } else {
            $result = $model->query("INSERT INTO shop_statusnotifier_shablon (name) VALUES ('".$name."')");
            $id = $result->lastInsertId();
            $template_path = wa()->getDataPath('plugins/receiveemail/templates/actions/frontend/' . $id . '.html', false, 'shop', true);
            $f = fopen($template_path, 'w');
            if (!$f) {
                throw new waException('Не удаётся сохранить шаблон. Проверьте права на запись ' . $template_path);
            }
            fwrite($f, $content);
            fclose($f);
        }
        
        $this->response['result'] = array('id' => $id, 'name' => $name);
    }
}