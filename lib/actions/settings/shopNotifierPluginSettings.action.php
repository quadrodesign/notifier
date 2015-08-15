<?php

class shopNotifierPluginSettingsAction extends waViewAction
{
  public function execute()
  {
    $workflow = new shopWorkflow();
    $modelNotifierConfig = new shopNotifierConfigModel();
    $modelNotifierTemplate = new shopNotifierTemplateModel();
    $state_names = array();
    foreach ($workflow->getAvailableStates() as $state_id => $state) {
      $state_names[$state_id] = $state['name'];
    }

    $all_notifications = $modelNotifierConfig->getAll();
    $templates = $modelNotifierTemplate->getAll();
//        timestamp
//        $time = strtotime('2015-04-01 18:11:44');
//        $day = strtotime('+20 minute', $time);
//        print_r(date('Y-m-d H:i:s', $day));

    $this->view->assign('cron', array(
      'command' => 'php ' . wa()->getConfig()->getRootPath() . '/cli.php shop notifierCheck',
    ));

    $this->view->assign('state_names', $state_names);
    $this->view->assign('templates', $templates);
    $this->view->assign('all_notifications', $all_notifications);
//        $this->view->assign('settings', $settings);
  }
}
