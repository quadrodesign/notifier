<?php

class shopStatusnotifierPluginSettingsGetnotificationController extends waJsonController
{
    public function execute()
    {  
        $id = waRequest::post('id');
        if(is_numeric($id)) {
            $model = new waModel();
            $result = $model->query("SELECT * FROM shop_statusnotifier_configuration WHERE id = '".$id."'")->fetchAssoc();
            $result['data_contact'] = (array)json_decode($result['data_contact']);
            
            if(count((array)$result['data_contact']['contact']) > 1) {
                $ids_contacts = implode(',',(array)$result['data_contact']['contact']);
            } else if(count((array)$result['data_contact']['contact']) == 1){
                foreach($result['data_contact']['contact'] as $c){
                    $ids_contacts = $c;
                }
            } 
            
            if(isset($ids_contacts)) {
                $result['contacts'] = array();
                $collection = new waContactsCollection('/id/'.$ids_contacts.'/');
                $result['contacts'] = $collection->getContacts('*');
            }
            
            if(count($result['data_contact']['group'])) {
                $result['groups'] = array();
                foreach($result['data_contact']['group'] as $gr) {
                    $result['groups'][$gr] = (array)$model->query("SELECT * FROM wa_contact_category WHERE id = '".$gr."'")->fetchAssoc();
                }
            }
            
            $result['state_name'] = (array)json_decode($result['state_name']);
            $this->response['result'] = $result; 
            $this->response['message'] = 'ok'; 
        } else {
            $this->response['message'] = 'fail';
        }
    }
}