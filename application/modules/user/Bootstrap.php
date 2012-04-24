<?php

/**
 * Slys
 *
 * Users authentification and ACL support for the Slys applications
 *
 * @author Evgheni Poleacov <evgheni.poleacov@gmail.com>
 */
class User_Bootstrap extends Zend_Application_Module_Bootstrap implements Slys_Api_Request_Requestable, Slys_Api_Notification_Notifier
{

    protected function _initAcl()
    {
        $this->bootstrap('Frontcontroller');
        $options = $this->getOptions();
        if(!empty($options['acl']['config']) && is_readable($options['acl']['config']))
            $config = new Zend_Config_Ini($options['acl']['config']);
        else
            $config = null;


        $this->getResource('Frontcontroller')->registerPlugin(
                new User_Plugin_Acl(new Slys_Acl($config))
        );

    }

    /**
     * @param Slys_Api_Notification_Registry $registry
     * @return void
     */
    public function publishNotifications(Slys_Api_Notification_Registry $registry)
    {
        $registry->register('user.login-action');
        $registry->register('user.logout-action');
    }

    public function onRequest(Slys_Api_Request $request)
    {
       switch ($request->getName()) {
            case 'user.get-acl':
                if (Zend_Controller_Front::getInstance()->hasPlugin('User_Plugin_Acl'))
                    $acl = Zend_Controller_Front::getInstance()->getPlugin('User_Plugin_Acl')->getAcl();
                else
                    $acl = false;
                $request->getResponse()->setData( $acl );
                break;
       }
    }

}