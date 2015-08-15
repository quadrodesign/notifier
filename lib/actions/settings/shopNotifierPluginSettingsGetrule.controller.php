<?php

class shopNotifierPluginSettingsGetruleController extends waJsonController
{
    public function execute()
    {  
        $id = waRequest::post('id');
        if(is_numeric($id)) {
            $modelNotifierRule = new shopNotifierRuleModel();
            $result = $modelNotifierRule->getById($id);
            $result['data_contact'] = json_decode($result['data_contact'],true);
            
            if(count((array)$result['data_contact']['contact']) > 1) {
                $ids_contact = implode(',',(array)$result['data_contact']['contact']);
            } else if(count((array)$result['data_contact']['contact']) == 1){
                foreach($result['data_contact']['contact'] as $c){
                    $ids_contact = $c;
                }
            } 
            
            if(isset($ids_contact)) {
                $result['contacts'] = array();
                $collection = new waContactsCollection('/id/'.$ids_contact.'/');
                $result['contacts'] = $collection->getContacts('*');
            }

            if(array_key_exists("group",$result['data_contact']) && count($result['data_contact']['group'])) {
                $result['groups'] = array();
                $modelContactCategory = new waContactCategoryModel();
                foreach($result['data_contact']['group'] as $group_id) {
                    $result['groups'][$group_id] = $modelContactCategory->getById($group_id);
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