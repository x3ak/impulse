<?php

class Members_Bootstrap extends Zend_Application_Module_Bootstrap
{
    /**
     * Plugins initialization
     */
    protected function _initPlugins()
    {
        $plugin = new Members_Plugin_Expire();

        Zend_Controller_Front::getInstance()->registerPlugin($plugin);
    }
}