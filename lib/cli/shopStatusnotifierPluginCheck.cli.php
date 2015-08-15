<?php

class shopStatusnotifierPluginCheckCli extends waCliController {

    public function execute()
    {
        $execute = new shopStatusnotifierPluginBackendCheckAction();
        $execute->execute();
    }
}