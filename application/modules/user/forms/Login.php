<?php

/**
 * SlyS
 *
 * @author     Evgheni Poleacov <evgheni.poleacov@gmail.com>
 *
 * @version    $Id: Login.php 1268 2011-07-19 08:23:32Z deeper $
 */
class User_Form_Login extends Zend_Form
{

    public function init()
    {

        $this->setMethod('POST');

        $loginElement = new Zend_Form_Element_Text('login');

        $loginElement->setLabel('Login:');
        $loginElement->setRequired(true);
        $this->addElement($loginElement);

        $passwordElement = new Zend_Form_Element_Password('password');

        $passwordElement->setLabel('Password:');
        $passwordElement->setRequired(true);
        $this->addElement($passwordElement);

        $submitElement = new Zend_Form_Element_Submit('submit');

        $submitElement->setLabel('Login');
        $submitElement->setIgnore(true);
        $submitElement->setAttrib('class', 'button');
        $this->addElement($submitElement);
    }

}