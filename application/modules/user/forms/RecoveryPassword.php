<?php

/**
 * SlyS
 *
 * @author     Evgheni Poleacov <evgheni.poleacov@gmail.com>
 *
 * @version    $Id: Profile.php 1012 2011-01-12 14:50:23Z deeper $
 */
class User_Form_RecoveryPassword extends Zend_Form
{

    public function init()
    {
        $element = new Zend_Form_Element_Text('password');
        $element->setLabel('Enter new password:');
        $element->setRequired(true)->setValidators(array(new Zend_Validate_Alnum()));
        $this->addElement($element);

        $element = new Zend_Form_Element_Text('confirm_password');
        $element->setLabel('Confirm new password:');
        $element->setRequired(true)->setValidators(array(new Zend_Validate_Alnum(),new Zend_Validate_Identical('password')));
        $this->addElement($element);

        $submitElement = new Zend_Form_Element_Submit('submit');
        $submitElement->setIgnore(true);
        $this->addElement($submitElement);
    }

}