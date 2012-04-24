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
        $filter['role'] = $this->getRequest()->getParam('role');

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
        $rolesModel = new User_Model_Roles();

        $id = $this->getRequest()->getParam('id');

        if (!empty($id)) {
            $user = $usersModel->getUser($id);
        } else {
            $user = new User_Model_Mapper_User();
        }

        foreach ($rolesModel->getList() as $role)
            $form->getElement('role_id')->addMultiOption($role->id, $role->name);

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

    public function rolesAction()
    {
        $rolesModel = new User_Model_Roles();
        $this->view->pager = $rolesModel->getRolesPager(
                        $this->getRequest()->getParam('page', 1),
                        $this->getRequest()->getParam('perPage', 20)
        );
    }

    /**
     * Edit user action
     * @return null
     */
    public function editRoleAction()
    {
        $form = new User_Form_Role();
        $rolesModel = new User_Model_Roles();

        $role = $rolesModel->getRole($this->getRequest()->getParam('id'), true);

        $form->populate($role->toArray());

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $result = $rolesModel->saveRole($role, $form->getValues());
            if($result)
                $this->_helper->getHelper('FlashMessenger')->addMessage('Role successful saved.');
            $this->_helper->getHelper('redirector')->goToRoute(array('module' => 'user', 'action' => 'roles'), 'admin', true);
            return;
        }

        $this->view->editRoleForm = $form;
    }

    /**
     * Delete role action
     */
    public function deleteRoleAction()
    {
        $id = $this->getRequest()->getParam('id');
        if (!empty($id)) {
            $rolesModel = new User_Model_Roles();
            $role = $rolesModel->getRole($id);
            $role->delete();
        }
        $this->_helper->getHelper('FlashMessenger')->addMessage('Role successful deleted.');
        $this->_helper->getHelper('redirector')->goToRoute(array('module' => 'user', 'action' => 'roles'), 'admin', true);
    }

    /**
     * Setting display action
     */
    public function settingsAction()
    {

    }
}