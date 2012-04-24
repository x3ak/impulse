<?php
class IndexController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $this->_helper->redirector->goToRoute(array(
            'module' => 'members',
            'controller' => 'admin',
            'action' => 'list'
        ), 'admin', true);
    }
}