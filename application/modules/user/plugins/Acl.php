<?php

class User_Plugin_Acl extends Zend_Controller_Plugin_Abstract
{

    public function routeShutdown(Zend_Controller_Request_Abstract $request)
    {
        $identity = Zend_Auth::getInstance()->getIdentity();

        $allowed = true;
        if(!empty($identity) && $identity->role != 'ADMIN') {
            $mca = implode('.', array($request->getModuleName(), $request->getControllerName(), $request->getActionName()));
            $acl = Zend_Registry::get('ACL');



            $allowed = in_array(strtolower($mca), $acl[$identity->role]);

            if($allowed == false ) {
                die;
            }

        }

        if(empty($identity) || $allowed == false) {
            $frontController = Zend_Controller_Front::getInstance();

//            $routeName = Zend_Controller_Front::getInstance()->getRouter()->getCurrentRouteName();
//
//            if ($routeName == 'admin')
//                $controller = 'admin';
//            else
//                $controller = 'auth';

            $request->setActionName('login')
                    ->setControllerName('auth')
                    ->setModuleName('user');


            if (!$frontController->hasPlugin('Zend_Layout_Controller_Plugin_Layout')) {
                $frontController->getParam('bootstrap')
                        ->registerPluginResource('layout')
                        ->bootstrap('layout');
            }

            $layout = $frontController
                    ->getPlugin('Zend_Layout_Controller_Plugin_Layout')
                    ->getLayout();

            $layout->setLayout('login');
        }
    }

}