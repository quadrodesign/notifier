<?php

class shopNotifierCheckCli extends waCliController {

    public function execute()
    {
        $plugin = wa()->getPlugin('notifier');

        $action = new shopNotifierPluginBackendCheckAction();
        $action->execute();
    }
}