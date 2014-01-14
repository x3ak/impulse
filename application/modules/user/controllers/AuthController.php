<?php

/**
 * SlyS
 *
 * @abstract   contains User_IndexController class, extending Zend_Controller_Action
 * @author     Evgheni Poleacov <evgheni.poleacov@gmail.com>
 *
 * @version    $Id: IndexController.php 1018 2011-01-13 14:28:24Z deeper $
 */

/**
 * User authorization pages
 */
class User_AuthController extends Zend_Controller_Action
{

    /**
     * Login action
     */
    public function loginAction()
    {
        $form = new User_Form_Login();

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            // collect the data from the user
            $loginUsername = $form->getValue('login');

            $loginPassword = sha1(trim($form->getValue('password')).User_Model_Users::$uniqueSalt);
            $slysAuth = Zend_Auth::getInstance();
            // do the authentication

            $identity = User_Model_DbTable_User::getInstance()->createQuery()
                    ->where('login = ?', $loginUsername)
                    ->andWhere('password = ?', $loginPassword)->fetchOne();



            if (empty($identity)) {
                $this->_helper->getHelper('FlashMessenger')->addMessage('Wrong combination of username and password');
            } else {
                $slysAuth->getStorage()->write($identity);

                $this->_helper->getHelper('FlashMessenger')->addMessage('You are successful logged!');
                $this->_redirect($this->getRequest()->getRequestUri());
            }
            $this->view->loginForm = $form;
        } else {
            $this->view->loginForm = $form;
        }

    }

    /**
     * Logout action
     */
    public function logoutAction()
    {
        $cache = $this->getInvokeArg('bootstrap')->getResource('cachemanager')->getCache('user');
        if(!empty($cache))
            $cache->clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array('user_session_data'));

        Zend_Auth::getInstance()->clearIdentity();

        $this->_redirect('/');
    }


}