<?php

/**
 * SlyS
 *
 * @author     Evgheni Poleacov <evgheni.poleacov@gmail.com>
 *
 * @version    $Id: Login.php 821 2010-12-17 16:24:51Z deeper $
 */
class User_Form_Widget_UserFilter extends Zend_Form_SubForm
{

    public function init()
    {
        $this->setMethod('POST');

        $roles = User_Model_DbTable_Role::getInstance()->getRoles();

        $options = $this->getView()->toList($roles->toArray(), 'id','name');

        $roleElement = new Zend_Form_Element_Select('role');
        $roleElement->addMultiOptions($options);
        $roleElement->setLabel('Role:');
        $roleElement->setRequired(true);
        $this->addElement($roleElement);
    }

}