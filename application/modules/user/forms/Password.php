<?php

/**
 * SlyS
 *
 * @author     Evgheni Poleacov <evgheni.poleacov@gmail.com>
 *
 * @version    $Id: Profile.php 1012 2011-01-12 14:50:23Z deeper $
 */
class User_Form_Password extends Zend_Form
{

    public function init()
    {
        $element = new Zend_Form_Element_Text('password');
        $element->setLabel('Enter old password:');
        $element->setRequired(true);
        $this->addElement($element);

        $element = new Zend_Form_Element_Text('new_password');
        $element->setLabel('Enter new password:');
        $element->setRequired(true);
        $this->addElement($element);

        $submitElement = new Zend_Form_Element_Submit('submit');
        $submitElement->setIgnore(true);
        $this->addElement($submitElement);
    }

}