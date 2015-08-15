<?php

class shopStatusnotifierPluginBackendCheckAction extends waViewAction {

    public function execute()
    {
        $model = new waModel();
        $log_model = new shopStatusnotifierLogModel();
        
        $n_date = array(
            'n' => 'week',
            'd' => 'days',
            'm' => 'hour',
            'h' => 'minute',
        );
        
        $all_notification = $model->query("SELECT * FROM shop_statusnotifier_configuration")->fetchAll();
        foreach($all_notification as $an){
            
            $last_event_date = strtotime($log_model->getLastDateByConfigId($an['id']));
            $last_event = strtotime('+'.$an['repeat_number_time'].' '.$n_date[$an['repeat_period']],$last_event_date);
            
            if(date('Y-m-d H:i:s', $last_event) < date('Y-m-d H:i:s')) {
                $states = (array)json_decode($an['state_name']);
                $last_time = strtotime('-'.$an['number_time'].' '.$n_date[$an['period']], $last_event_date);
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
                $an['data_contact'] = json_decode($an['data_contact']);
                foreach($an['data_contact']->contact as $con){
                    $user = new waContact($con);
                    $email = array();
                    $email = $user->get('email');
                    $emails[$email[0]['value']] = $user->get('name');
                }
                
                foreach($an['data_contact']->group as $gr){
                    $contacts = $model->query("SELECT * FROM wa_contact_categories WHERE category_id = '".$gr."'")->fetchAll('contact_id');
                    
                    foreach($contacts as $key => $con) {
                        $user = new waContact($key);
                        $email = array();
                        $email = $user->get('email');
                        $emails[$email[0]['value']] = $user->get('name');
                    }
                }
                
                $view = wa()->getView();
                
                if(empty($an['send_email'])) {
                    $shop_config =  wa('shop')->getConfig();
                    $from = $shop_config->getGeneralSettings('email');
                } else {
                    if(!self::isValidEmail($an['send_email'])) {
                        $shop_config =  wa('shop')->getConfig();
                        $from = $shop_config->getGeneralSettings('email');
                        if(!$from){
                            $from = 'admin@admin.ru';
                        }
                    } else {
                        $from = $an['send_email'];
                    }
                }
                
                $body = file_get_contents(wa()->getDataPath('plugins/receiveemail/templates/actions/frontend/' . $an['theme'] . '.html', false, 'shop', true));
                
                if($an['group_senders'] == 1) {
                    if($an['save_to_order_log'] == 1) {
                        $order_log_model = new shopOrderLogModel();
                        foreach($orders as $order) {
                            $order_log_model->add(array(
                                'order_id' => $order['id'],
                                'contact_id' => wa()->getUser()->getId(),
                                'before_state_id' => $order['state_id'],
                                'after_state_id' => $order['state_id'],
                                'text' => 'Отослано уведомление на емайлы из оповещания '.$an['config_name'],
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
                        if($an['save_to_order_log'] == 1) {
                            $order_log_model = new shopOrderLogModel();
                            $order_log_model->add(array(
                                'order_id' => $order['id'],
                                'contact_id' => wa()->getUser()->getId(),
                                'before_state_id' => $order['state_id'],
                                'after_state_id' => $order['state_id'],
                                'text' => 'Отослано уведомление на емайлы из оповещания '.$an['config_name'],
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
                
                $log_model->add(array('config_id' => $an['id']));
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