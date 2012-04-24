<?php

/**
 * Slys
 *
 * Template layout switcher. Used to check and switch layout for the current theme
 * such file exists
 *
 * @author Serghei Ilin <criolit@gmail.com>
 */
class Navigation_Plugin_Init extends Zend_Controller_Plugin_Abstract
{
    /**
     * On dispatch loop startup initializing global navigation
     * @param Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        Zend_Registry::set('Zend_Navigation', Navigation_Model_Navigation::getInstance()->getNavigation());
    }
}