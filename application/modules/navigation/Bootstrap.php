<?php

/**
 * Slys
 *
 * Navigation module bootstrap class
 *
 * @author Serghei Ilin <criolit@gmail.com>
 * @version $Id: Bootstrap.php 1139 2011-01-28 16:07:30Z criolit $
 */
class Navigation_Bootstrap extends Zend_Application_Module_Bootstrap implements Slys_Api_Notification_Notifiable
{
    protected function _initRegisterHelper()
    {
        if (!$this->getApplication()->hasPluginResource('view'))
            $this->getApplication()->registerPluginResource('view');

        $this->getApplication()->bootstrap('view');

        $this->getApplication()->getResource('view')->registerHelper(
                new Navigation_View_Helper_AdminCurrentSubmenu(),
                'adminCurrentSubmenu'
        );

        $this->getApplication()->getResource('view')->registerHelper(
                new Navigation_View_Helper_ArrayTreeToTable(),
                'arrayTreeToTable'
        );
    }

    protected function _initPlugins()
    {
        Zend_Controller_Front::getInstance()->registerPlugin(new Navigation_Plugin_Init());
    }

    public function onNotification(Slys_Api_Notification $notification)
    {
        switch($notification->getName()) {
            case 'sysmap.item-updated':
                $params = $notification->getParams();
                Navigation_Model_Navigation::getInstance()->updateItemHash($params['identifier'], $params['new_identifier']);
                break;

            case 'sysmap.item-deleted':
                $params = $notification->getParams();
                Navigation_Model_Navigation::getInstance()->deleteItemByIdentifier($params['identifier']);
                break;
        }
    }
}