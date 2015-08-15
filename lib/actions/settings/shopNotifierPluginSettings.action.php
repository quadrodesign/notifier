<?php

class shopNotifierPluginSettingsAction extends waViewAction
{
    public function execute()
    {
        $workflow = new shopWorkflow();
        $model = new waModel();
        $state_names = array();
        foreach ($workflow->getAvailableStates() as $state_id => $state) {
            $state_names[$state_id] = $state['name'];
        }
        
        $all_notifications = $model->query("SELECT * FROM shop_notifier_config ")->fetchAll();
        $themes = $model->query("SELECT * FROM shop_notifier_template ")->fetchAll();
//        timestamp
//        $time = strtotime('2015-04-01 18:11:44');
//        $day = strtotime('+20 minute', $time);
//        print_r(date('Y-m-d H:i:s', $day));
        
        $this->view->assign('cron', array(
            'command' => 'php '.wa()->getConfig()->getRootPath().'/cli.php shop notifierCheck',
        ));
        
        $this->view->assign('state_names', $state_names);
        $this->view->assign('themes', $themes);
        $this->view->assign('all_notifications', $all_notifications);
//        $this->view->assign('settings', $settings);
    }       
}
