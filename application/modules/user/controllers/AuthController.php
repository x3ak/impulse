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
            $loginPassword = md5($form->getValue('password'));
            $slysAuth = Zend_Auth::getInstance();
            // do the authentication

            $authAdapter = $this->_getAuthAdapter($loginUsername, $loginPassword);
            $result = $slysAuth->authenticate($authAdapter);
            if (!$result->isValid()) {
                $form->setDecorators(array('Errors', 'FormElements', 'Form'));
                $form->addError('Wrong combination of username and password');
            } else {
                $identity = $authAdapter->getResultRowObject(null, 'password');
                $identity = User_Model_DbTable_User::getInstance()->getUser($identity->id);

                $slysAuth->getStorage()->write($identity);

                Slys_Api::getInstance()->notify(null, 'user.login-action');

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

        Slys_Api::getInstance()->notify(null, 'user.logout-action');

        $this->_redirect('/');
    }

    /**
     * User login box
     */
    public function loginBoxAction()
    {
        if(Zend_Auth::getInstance()->hasIdentity())
            $this->_forward ('user-box', 'profile');
        else
            $this->loginAction();
    }

    /**
     * Return doctrine based auth adapter
     * @param string $username
     * @param string $password
     * @return ZendX_Doctrine_Auth_Adapter
     */
    protected function _getAuthAdapter($username, $password)
    {
        $authAdapter = new ZendX_Doctrine_Auth_Adapter(Doctrine_Manager::getInstance()->getCurrentConnection());
        $authAdapter->setTableName('User_Model_Mapper_User u')
                ->setIdentityColumn('u.login')
                ->setCredentialColumn('u.password')
                ->setIdentity($username)
                ->setCredential($password);

        return $authAdapter;
    }

     /**
     * Forgot password action
     * @return void
     */
    public function forgotAction()
    {
	$userModel = new User_Model_Users();
        $forgotForm = new User_Form_ForgotPassword();

        if($this->getRequest()->isPost() && $forgotForm->isValid($this->getRequest()->getPost())) {
            $user = $userModel->getUserByEmail($forgotForm->getElement('email')->getValue());
            if($user) {
            	$userModel->setPasswordToken($user, 1);
                $this->_sendPasswordRecoveryEmail($user);
        	    $this->_redirect($this->_helper->url->url(array(
                      'action'=>'check-email',
                      'module'=>'user',
                      'controller'=>'auth'),
                 'default', true), array('prependBase' => false));
            }
        }
        $this->view->forgotForm = $forgotForm;
    }

    protected function _sendPasswordRecoveryEmail(User_Model_Mapper_User $user)
    {
        $templateName = 'password_recovery_email';

        $apiRequest = new Slys_Api_Request($this, 'email.get-tpl-by-sysname', array('sysname'=> $templateName));
        $tpl = $apiRequest->proceed()->getResponse()->getFirst();

        if(!empty($tpl)) {
            $params = array(
                'sysname'=> $templateName,
                'to'=> $user->email,
                'token'=>$user->token,
                'user_full_name' => $user->firstname.' '.$user->lastname,
                'recovery_link' => 'http://'.$this->getRequest()->getHttpHost().$this->view->url(
                        array('module'=>'user', 'controller'=>'auth', 'action'=>'recovery', 'token'=>$user->token), 'default', true
                )
            );
            $apiRequest = new Slys_Api_Request($this, "email.send-email-template", $params);
            $response = $apiRequest->proceed()->getResponse()->getFirst();
        }
    }

    public function checkEmailAction()
    {

    }

    /**
     * Forgot password action
     * @return void
     */
    public function recoveryAction()
    {
	$userModel = new User_Model_Users();
        $passwordForm = new User_Form_RecoveryPassword();
        $token = $this->getRequest()->getParam('token');
        if(empty($token))
            throw new Zend_Exception('Wrong or expired recovery token');

        if($this->getRequest()->isPost() && $passwordForm->isValid($this->getRequest()->getPost())) {
            $user = $userModel->getUserByToken($token);
            if($user) {
            	$userModel->savePassword($user, $passwordForm->getElement('password')->getValue());
                $userModel->resetPasswordToken($user);
                $this->_helper->getHelper('FlashMessenger')->addMessage('Password changed.');
        	$this->_redirect($this->_helper->url->url(array(
                      'module'=>'default'),
                 'default', true), array('prependBase' => false));
            } else {
                throw new Zend_Exception('Wrong or expired recovery token');
            }
        }
        $this->view->recoveryForm = $passwordForm;
    }
}