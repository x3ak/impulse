<?php

class AdminController extends Zend_Controller_Action
{

    public function indexAction()
    {
        $this->_helper->redirector->goToRoute(array(
            'module' => 'members',
            'controller' => 'admin',
        ), 'default', true);
    }
}