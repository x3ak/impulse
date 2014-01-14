<?php

/**
 * SlyS
 *
 * @abstract   contains User_AdminController class, extending Zend
 * @author     Evgheni Poleacov <evgheni.poleacov@gmail.com>
 *
 * @version    $Id: AdminController.php 1268 2011-07-19 08:23:32Z deeper $
 */

/**
 * User administrator panel
 */
class User_AdminController extends Zend_Controller_Action
{

    /**
     * User module admin dashboard
     */
    public function indexAction()
    {

    }

    /**
     * Administrator login action
     */
    public function loginAction()
    {
        $this->_forward('login', 'auth');
    }

    /**
     * Users list
     *
     * @paramsform User_Form_Widget_UserFilter
     */
    public function usersAction()
    {
        $filter = array();

        $usersModel = new User_Model_Users();
        $this->view->pager = $usersModel->getUsersPager(
            $this->getRequest()->getParam('page', 1),
            $this->getRequest()->getParam('perPage', 20),
            $filter
        );
    }

    /**
     * Edit user action
     * @return null
     */
    public function editUserAction()
    {
        $form = new User_Form_User();
        $usersModel = new User_Model_Users();

        $id = $this->getRequest()->getParam('id');

        if (!empty($id)) {
            $user = $usersModel->getUser($id);
        } else {
            $user = new User_Model_Mapper_User();
        }


        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $result = $usersModel->saveUser($user, $form->getValues());
            $this->_helper->getHelper('FlashMessenger')->addMessage('User successful saved.');
            $this->_helper->getHelper('redirector')->goToRoute(array('module' => 'user', 'action' => 'users'), 'admin', true);

            return;
        } elseif(!$this->getRequest()->isPost()) {
            $form->populate($user->toArray());
        }

        $this->view->editUserForm = $form;
    }

    /**
     * Delete user action
     */
    public function deleteUserAction()
    {
        $id = $this->getRequest()->getParam('id');
        if (!empty($id)) {
            $usersModel = new User_Model_Users();
            $user = $usersModel->getUser($id);
            $user->delete();
        }
        $this->_helper->getHelper('FlashMessenger')->addMessage('User successful deleted.');
        $this->_helper->getHelper('redirector')->goToRoute(array('module' => 'user', 'action' => 'users'), 'admin', true);
    }

}