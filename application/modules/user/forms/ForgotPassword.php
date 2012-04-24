<?php

/**
 * SlyS
 *
 * @author     Evgheni Poleacov <evgheni.poleacov@gmail.com>
 *
 * @version    $Id: Login.php 821 2010-12-17 16:24:51Z deeper $
 */
class User_Form_ForgotPassword extends Zend_Form
{

    public function init()
    {

        $this->setMethod('POST');

        $email = new Zend_Form_Element_Text('email');
        $email->setRequired()->addValidator(new Zend_Validate_EmailAddress)->setLabel('email')
              ->addValidator(new Slys_Validate_Doctrine_RecordExists(new User_Model_Mapper_User,'email'));
        $this->addElement($email);

        $submitElement = new Zend_Form_Element_Submit('submit');

        $submitElement->setLabel('Recover');
        $submitElement->setIgnore(true);
        $this->addElement($submitElement);
    }

}