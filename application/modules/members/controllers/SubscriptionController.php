<?php
/**
 * Subscription administration
 */
class Members_SubscriptionController extends Zend_Controller_Action
{
    public function init()
    {
        if( $this->getRequest()->isXmlHttpRequest() ) {
            $this->_helper->layout->disableLayout();
        }
        parent::init();
    }

    /**
     * Subscription dashboard
     */
    public function indexAction()
    {

    }

    public function listAction()
    {
        $list = Members_Model_DbTable_SubscriptionType::getInstance()->findAll();
        $this->view->list = $list;
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
        if(!empty($id)) {
            $mapper = Members_Model_DbTable_SubscriptionType::getInstance()->findOneBy('id', $id);
        } else {
            $mapper = new Members_Model_Mapper_SubscriptionType();
        }


        $form = new Members_Form_SubscriptionType_Edit();


        if($this->getRequest()->isPost()) {

            if($form->isValid($this->getRequest()->getParams())) {

                $mapper->fromArray($form->getValues());

                $mapper->save();

                $this->_helper->getHelper('FlashMessenger')->addMessage('subscription saved');
                $this->_helper->redirector->goToRoute(array(
                    'module' => 'members',
                    'controller' => 'subscription',
                    'action' => 'list'
                ), 'default', true);
            }
        }

        $form->populate($mapper->toArray());

        $this->view->form = $form;

    }

    public function editSubscriptionAction()
    {
        $id = $this->getRequest()->getParam('id');
        if(!empty($id)) {
            $mapper = Members_Model_DbTable_Subscription::getInstance()->findOneBy('id', $id);
        } else {
            $mapper = new Members_Model_Mapper_Subscription();
        }


        $form = new Members_Form_Subscription_Edit();


        if($this->getRequest()->isPost()) {

            if($form->isValid($this->getRequest()->getParams())) {

                $mapper->fromArray($form->getValues());

                $mapper->save();

                $this->_helper->getHelper('FlashMessenger')->addMessage('subscription saved');
                $this->_helper->redirector->goToRoute(array(
                    'module' => 'members',
                    'action' => 'view',
                    'id' => $mapper->member_id
                ), 'admin', true);
            }
        }

        $form->populate($mapper->toArray());

        $this->view->form = $form;

    }

    public function viewAction()
    {
        $id = $this->getRequest()->getParam('id');
        if(!empty($id)) {
            $mapper = Members_Model_DbTable_Subscription::getInstance()->findOneBy('id', $id);
        }

        if(empty($mapper)) {
            $this->_helper->getHelper('FlashMessenger')->addMessage('subscription_not_found');
            $this->_helper->redirector->goToRoute(array(
                                                        'module' => 'subscription',
                                                        'action' => 'list'
                                                   ), 'admin', true);
        }

        $this->view->mapper = $mapper;
    }
}
