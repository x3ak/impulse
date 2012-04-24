<?php

/**
 * SlyS
 *
 * @author Evgheni Poleacov <evgheni.poleacov@gmail.com>
 *
 * @version $Id: Login.php 821 2010-12-17 16:24:51Z deeper $
 */
class User_Form_Registration extends Zend_Form
{

    /**
     * Initialization
     */
    public function init()
    {
        $this->setMethod('POST');
        $this->setAttrib('autocomplete', 'off');

        $loginElement = new Zend_Form_Element_Text('login');
        $loginElement->setLabel('login');
        $loginElement->addValidator(new Slys_Validate_Doctrine_NoRecordExists(new User_Model_Mapper_User,'login'));
        $loginElement->setRequired(true);
        $this->addElement($loginElement);

        $passwordElement = new Zend_Form_Element_Password('password');
        $passwordElement->setLabel('password');
        $passwordElement->setRequired(true);
        $this->addElement($passwordElement);

        $roleModel = new User_Model_Roles();
        $roles = $roleModel->getRegistrationRoles();
        $options = array(''=>'');
        foreach($roles as $role) {
            $options[$role->id] = $role->name;
        }

        $type = new Zend_Form_Element_Select('role_id');
        $type->addMultiOptions($options)->setRequired()->setLabel('user_type');
        $this->addElement($type);

        $firstname = new Zend_Form_Element_Text('firstname');
        $firstname->setRequired()->addValidator(new Zend_Validate_Alpha)->setLabel('firstname');
        $this->addElement($firstname);

        $lastname = new Zend_Form_Element_Text('lastname');
        $lastname->setRequired()->addValidator(new Zend_Validate_Alpha)->setLabel('lastname');
        $this->addElement($lastname);

        $patronymic = new Zend_Form_Element_Text('patronymic');
        $patronymic->setRequired()->addValidator(new Zend_Validate_Alpha)->setLabel('patronymic');
        $this->addElement($patronymic);

        $birth = new Zend_Form_Element_Text('birth');
        $birth->setRequired()->setLabel('birth date');
        $this->addElement($birth);

        $email = new Zend_Form_Element_Text('email');
        $email->setRequired()->addValidator(new Zend_Validate_EmailAddress)->setLabel('email')
              ->addValidator(new Slys_Validate_Doctrine_NoRecordExists(new User_Model_Mapper_User,'email'));
        $this->addElement($email);

        $phone = new Zend_Form_Element_Text('phone');
        $phone->setRequired()->addValidator(new Zend_Validate_Digits)->setLabel('phone');
        $this->addElement($phone);

        $submitElement = new Zend_Form_Element_Submit('submit');
        $submitElement->setLabel('register');
        $submitElement->setIgnore(true);
        $this->addElement($submitElement);
    }

    public function isValid($data)
    {
        $this->populate($data);
        $this->getView()->RegValidation($this);
        return parent::isValid($data);
    }
}
