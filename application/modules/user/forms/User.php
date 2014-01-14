<?php

/**
 * SlyS
 *
 * @author     Evgheni Poleacov <evgheni.poleacov@gmail.com>
 *
 * @version    $Id: User.php 1329 2011-10-20 20:57:55Z zak $
 */
class User_Form_User extends Zend_Form
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
        $loginElement->setRequired(true);
        $this->addElement($loginElement);


        $password = new Zend_Form_Element_Text('password');
        $password->setLabel('Password');
        $this->addElement($password);

        $role = new Zend_Form_Element_Select('role');
        $role->setLabel('Role');
        $role->setMultiOptions(array(
            'MANAGER' => 'MANAGER',
            'ADMIN' => 'ADMIN'
        ));
        $this->addElement($role);

        $firstname = new Zend_Form_Element_Text('firstname');
        $firstname->setRequired()->addValidator(new Zend_Validate_Alpha)->setLabel('firstname');
        $this->addElement($firstname);

        $lastname = new Zend_Form_Element_Text('lastname');
        $lastname->setRequired()->addValidator(new Zend_Validate_Alpha)->setLabel('lastname');
        $this->addElement($lastname);

        $birth = new Zend_Form_Element_Text('birth');
        $birth->setLabel('birth date');
        $this->addElement($birth);

        $email = new Zend_Form_Element_Text('email');
        $email->setRequired()->addValidator(new Zend_Validate_EmailAddress)->setLabel('email');
        $this->addElement($email);

        $phone = new Zend_Form_Element_Text('phone');
        $phone->addValidator(new Zend_Validate_Digits)->setLabel('phone');
        $this->addElement($phone);

        $zip = new Zend_Form_Element_Text('zip');
        $zip->setLabel('zip');
        $this->addElement($zip);

        $address = new Zend_Form_Element_Text('address');
        $address->setLabel('address');
        $this->addElement($address);

        $submitElement = new Zend_Form_Element_Submit('submit');
        $submitElement->setLabel('save');
        $submitElement->setIgnore(true);
        $this->addElement($submitElement);
    }

    public function isValid($data)
    {
        $this->populate($data);
        return parent::isValid($data);
    }

    public function setDefaults(array $defaults)
    {
        $defaults['password'] = '';
        return parent::setDefaults($defaults);
    }


}