<?php
/**
 * Products administration
 */
class Products_AdminController extends Zend_Controller_Action
{
    /**
     * list of products
     */
    public function indexAction() {
        $list = Products_Model_DbTable_Product::getInstance()->findAll();
        $this->view->list = $list;
    }

    /**
     * Edit/add product
     */
    public function editAction() {
        $form = new Products_Form_Edit();

        $id = $this->getRequest()->getParam('id');
        if(empty($id)) {
            $mapper = new Products_Model_Mapper_Product();
        } else {
            $mapper = Products_Model_DbTable_Product::getInstance()->findOneBy('id', $id);
        }

        $form->populate($mapper->toArray());

        if($this->getRequest()->isPost()) {
            if($form->isValid($this->getRequest()->getPost())) {
                $mapper->fromArray($form->getValues());
                $mapper->save();

                $this->_helper->getHelper('FlashMessenger')->addMessage('product_was_saved');
                $this->_helper->redirector->goToRoute(array(
                    'module' => 'products',
                    'action' => 'index'
                ), 'admin', true);
            }
        }

        $this->view->form = $form;
    }

    public function buyAction()
    {
        $id = $this->getRequest()->getParam('id');

        if(!empty($id)) {
            $mapper = Products_Model_DbTable_Product::getInstance()->findOneBy('id', $id);
            $mapper->buy();
        }

        $list = Products_Model_DbTable_Product::getInstance()->findAll();
        $this->view->list = $list;
        $this->view->productsCount = $list->count();
    }
}
