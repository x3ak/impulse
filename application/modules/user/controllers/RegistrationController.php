<?php

/**
 * SlyS
 *
 * @abstract   contains User_RegistrationController class, extending Zend_Controller_Action
 * @author     Evgheni Poleacov <evgheni.poleacov@gmail.com>
 *
 * @version    $Id: ProfileController.php 1018 2011-01-13 14:28:24Z deeper $
 */

/**
 * User registration pages
 */
class User_RegistrationController extends Zend_Controller_Action
{
    /**
     * Display registration page
     */
    public function indexAction()
    {
        $userModel = new User_Model_Users();
        $regForm = new User_Form_Registration();
        if($this->getRequest()->isPost() && $regForm->isValid($this->getRequest()->getPost())) {
            $user = $userModel->addUser($regForm->getValues());
            if($user) {
                $this->_sendRegistrationEmail($user);
        	    $this->_redirect($this->_helper->url->url(array(
                      'action'=>'check-email',
                      'module'=>'user',
                      'controller'=>'registration',
        	    	  md5('email')=>base64_encode($user->email)),
                 'default', true), array('prependBase' => false));
            }
        }
        $this->view->regForm = $regForm;
    }

    /**
     * Pag with check email message
     */
    public function checkEmailAction()
    {

    }

    /**
     * Send registration confirmation email
     * @param User_Model_Mappers_User $user
     */
    protected function _sendRegistrationEmail(User_Model_Mapper_User $user)
    {
        $templateName = 'registration_email_'.$user->Role->name;
        $userModel = new User_Model_Users();
        $apiRequest = new Slys_Api_Request($this, 'email.get-tpl-by-sysname', array('sysname'=> $templateName));
        $tpl = $apiRequest->proceed()->getResponse()->getFirst();
        $manager = $userModel->getManagerByUser($user);
        if(!empty($tpl)) {
            $params = array(
                'sysname'=> $templateName,
                'to'=> $user->email,
                'login'=>$user->login,
                'password'=>$user->password,
                'user_full_name' => $user->firstname.' '.$user->lastname,
                'user_cars_link' => 'http://'.$this->getRequest()->getHttpHost().$this->view->url(
                        array('module'=>'cars', 'controller'=>'owner', 'id'=>$user->id), 'default', true
                ),
                'default_manager_info'=> $manager->firstname.' '.$manager->lastname
            );
            $apiRequest = new Slys_Api_Request($this, "email.send-email-template", $params);
            $response = $apiRequest->proceed()->getResponse()->getFirst();
        }

    }



}