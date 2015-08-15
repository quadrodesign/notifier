<?php

class shopNotifierPluginSettingsSearchcontactController extends waJsonController
{
    public function execute()
    {
        mb_internal_encoding("UTF-8");
        $query = waRequest::post('query');
        $query = strtolower($query);
        
        $collection_by_email = new waContactsCollection('/search/email*='.$query.'/');
        $contacts_by_email = $collection_by_email->getContacts('*');
        
        $collection_by_name = new waContactsCollection('/search/name*='.$query.'/');
        $contacts_by_name = $collection_by_name->getContacts('*');
        
        if(is_array($contacts_by_email) && is_array($contacts_by_name)){
            $contacts = array_merge($contacts_by_email, $contacts_by_name);
        } else {
            if(is_array($contacts_by_email) || is_array($contacts_by_name)){
                $contacts = is_array($contacts_by_email) ? $contacts_by_email : $contacts_by_name;
            } else {
                $contacts = array();
            }
        }
        
        $modelContactCategory = new waContactCategoryModel();
        $result = $modelContactCategory->getByField('name',$query);
//        query("SELECT * FROM wa_contact_category WHERE name LIKE '%".mysql_escape_string($query)."%'")->fetchAll();
        
        if($result) {
            $search['group'] = $result;
            $search['contacts'] = $contacts;
        } else {
            $search['group'] = array();
            $search['contacts'] = $contacts;
        }
        
        $this->response['search'] = $search;  
    }
}