<?php

class shopNotifierPlugin extends shopPlugin
{
  public static function path($id = null)
  {
    return empty($id) ? false : wa()->getDataPath('plugins/notifier/' . $id . '.html', false, 'shop', true);
  }

}
