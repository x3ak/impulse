<?php

/**
 * 	SlyS
 *
 * @abstract   contains User_ProfileController class, extending Zend_Controller_Action
 * @author     Evgheni Poleacov <evgheni.poleacov@gmail.com>
 *
 * @version    $Id: ProfileController.php 1268 2011-07-19 08:23:32Z deeper $
 */

/**
 * User profile pages
 */
class User_ProfileController extends Zend_Controller_Action
{
    /**
     * Display&Edit user profile form
     */
    public function indexAction()
    {
        $identity = Zend_Auth::getInstance()->getIdentity();
        $userId = $identity->id;
        $userModel = new User_Model_Users();
        $user = $userModel->getUser($userId);
        $form = new User_Form_Profile();
        $form->populate($user->toArray());
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $result = $userModel->saveProfile($user, $form->getValues());
            if ($result) {
                $this->_helper->getHelper('FlashMessenger')->addMessage('Your profile saved.');
                $this->_helper->redirector->gotoUrlAndExit($this->getRequest()->getRequestUri());
            }
        }
        $this->view->profile = $form;
    }

    /**
     * Change user password page
     */
    public function changePasswordAction()
    {
        $form = new User_Form_Password();
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $userModel = new User_Model_Users();
            $identity = Zend_Auth::getInstance()->getIdentity();
            $userId = $identity->id;
            $user = $userModel->getUser($userId);
            $result = $userModel->savePassword($user, $form->getValue('new_password'), $form->getValue('password'));

            if ($result) {
                $this->_helper->getHelper('FlashMessenger')->addMessage('Your password was changed.');
            } else {
                $this->_helper->getHelper('FlashMessenger')->addMessage('Your password was NOT changed.');
            }
            $this->_redirect($this->getRequest()->getRequestUri());
        }
        $this->view->passwordForm = $form;
    }

    /**
     * Display user box
     * @paramsform User_Form_Widget_UserBox
     */
    public function userBoxAction()
    {
        $this->view->boxType = $this->getRequest()->getParam('box_type');
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            $this->_forward('login','auth','user');
        } else {
            
            $user = Zend_Auth::getInstance()->getIdentity();
            $this->view->userIdentity = $user;
            $cacheId = $user->login.'manager';
            $cache = $this->getInvokeArg('bootstrap')->getResource('cachemanager')->getCache('main');

            if (!($manager = $cache->load($cacheId))) {
                $userModel = new User_Model_Users();
                $manager = $userModel->getManagerByUser($user);
                $cache->save($manager, $cacheId , array('user_session_data'));
            }

            $this->view->manager = $manager;
        }
    }
}