<?php

class shopNotifierPluginBackendCheckAction extends waViewAction {

    public function execute()
    {
        $model = new waModel();
        $log_model = new shopNotifierPluginLogModel();
        
        $n_date = array(
            'n' => 'week',
            'd' => 'days',
            'm' => 'hour',
            'h' => 'minute',
        );
        
        $all_notifications = $model->query("SELECT * FROM shop_notifier_config")->fetchAll();
        foreach($all_notifications as $notification){
            
            $last_event_date = strtotime($log_model->getLastDateByConfigId($notification['id']));
            $last_event = strtotime('+'.$notification['repeat_number_time'].' '.$n_date[$notification['repeat_period']],$last_event_date);
            
            if(date('Y-m-d H:i:s', $last_event) < date('Y-m-d H:i:s')) {
                $states = (array)json_decode($notification['state_name']);
                $last_time = strtotime('-'.$notification['number_time'].' '.$n_date[$notification['period']], $last_event_date);
                $orders = array();
                $orders_new = array();
                
                foreach($states as $s){
                    if($s == 'new') {
                        $collection = new shopOrdersCollection('search/state_id=new&create_datetime<'.date('Y-m-d H:i:s', $last_time));
                        $orders_new = $collection->getOrders('*,params,items,contact');
                    } else {
                        $states_without_new[] = $s;
                    }
                }
                
                $state_for_collection = is_array($states_without_new) ? implode('||', $states_without_new) : $states_without_new[0];
                $collection = new shopOrdersCollection('search/state_id='.$state_for_collection.'&update_datetime<'.date('Y-m-d H:i:s', $last_time));
                $orders = $collection->getOrders('*,params,items,contact');
                
                if(is_array($orders_new)) {
                    $orders = array_merge($orders, $orders_new);
                }
                
                $emails = array();
                $notification['data_contact'] = json_decode($notification['data_contact']);
                foreach($notification['data_contact']->contact as $contact){
                    $user = new waContact($contact);
//                    $email = array();
                    $email = $user->get('email');
                    $emails[$email[0]['value']] = $user->get('name');
                }


                if (!empty($notification['data_contact']->group)){
                    $modelContactCategory = new waContactCategoryModel();
                    foreach($notification['data_contact']->group as $group){
                        $contacts = $modelContactCategory->getByField('category_id', $group);
//                      ->query("SELECT * FROM wa_contact_categories WHERE category_id = '".$gr."'")->fetchAll('contact_id');

                        foreach($contacts as $key => $contact) {
                            $user = new waContact($key);
                            $email = array();
                            $email = $user->get('email');
                            $emails[$email[0]['value']] = $user->get('name');
                        }
                    }

                }
                $view = wa()->getView();
                
                if(empty($notification['send_email'])) {
                    $shop_config =  wa('shop')->getConfig();
                    $from = $shop_config->getGeneralSettings('email');
                } else {
                    if(!self::isValidEmail($notification['send_email'])) {
                        $shop_config =  wa('shop')->getConfig();
                        $from = $shop_config->getGeneralSettings('email');
                        if(!$from){
                            $from = 'admin@admin.ru';
                        }
                    } else {
                        $from = $notification['send_email'];
                    }
                }
                
                $body = file_get_contents(shopNotifierPlugin::path($notification['theme']));
                //TODO: File read error
                if($notification['group_senders'] == 1) {
                    if($notification['save_to_order_log'] == 1) {
                        $order_log_model = new shopOrderLogModel();
                        foreach($orders as $order) {
                            $order_log_model->add(array(
                                'order_id' => $order['id'],
                                'contact_id' => wa()->getUser()->getId(),
                                'before_state_id' => $order['state_id'],
                                'after_state_id' => $order['state_id'],
                                'text' => 'Отправлено уведомление на адреса из оповещания '.$notification['config_name'],
                                'action_id' => 'comment',
                            ));
                        }
                    }
                    
                    $view->clearAllAssign();
                    $view->assign('orders', $orders);
                    $subject_string = 'Обратите внимание на заказы';
                    
                    $subject = $view->fetch('string:'.$subject_string);
                    $body = $view->fetch('string:'.$body);
                    
                    $message = new waMailMessage($subject, $body);
                    $message->setTo($emails);
                    $message->setFrom($from);
                    $message->send();
                } else {
                    foreach($orders as $order){
                        if($notification['save_to_order_log'] == 1) {
                            $order_log_model = new shopOrderLogModel();
                            $order_log_model->add(array(
                                'order_id' => $order['id'],
                                'contact_id' => wa()->getUser()->getId(),
                                'before_state_id' => $order['state_id'],
                                'after_state_id' => $order['state_id'],
                                'text' => 'Отослано уведомление на емайлы из оповещания '.$notification['config_name'],
                                'action_id' => 'comment',
                            ));
                        }
                        
                        $view->clearAllAssign();
                        $view->assign('order', $order);
                        $subject_string = 'Заказ '.shopHelper::encodeOrderId($order['id']);
                        
                        $subject = $view->fetch('string:'.$subject_string);
                        $body = $view->fetch('string:'.$body);
                        
                        $message = new waMailMessage($subject, $body);
                        $message->setTo($emails);
                        $message->setFrom($from);
                        $message->send();
                    }
                }
                
                $log_model->add(array('config_id' => $notification['id']));
            }
        }
    }
    
    private function isValidEmail($email)
    {
        if(!$email_validator) {
            $email_validator = new waEmailValidator(
                array( 'required' => true ),
                array( 'required' => _wp('Email is required') )
            );
        }
        return $email_validator->isValid($email);
    }
}