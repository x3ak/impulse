<?php

/**
 * SlyS
 *
 * @author     Evgheni Poleacov <evgheni.poleacov@gmail.com>
 *
 * @version    $Id: Profile.php 1016 2011-01-13 13:16:42Z deeper $
 */
class User_Form_Profile extends Zend_Form
{

    public function init()
    {
        $action = $this->getView()->url(
                array('action'=>'index','controller'=>'profile','module'=>'user'),
                'default',
                true);
        $this->setMethod('POST');
        $this->setAction($action);

        $element = new Zend_Form_Element_Text('firstname');
        $element->setLabel('Firstname:');
        $element->setRequired(true);
        $this->addElement($element);

        $element = new Zend_Form_Element_Text('lastname');
        $element->setLabel('Lastname:');
        $element->setRequired(true);
        $this->addElement($element);

        $element = new Zend_Form_Element_Text('patronymic');
        $element->setLabel('Patronymic:');
        $element->setRequired(true);
        $this->addElement($element);

        $element = new Zend_Form_Element_Text('email');
        $element->setLabel('Email:');
        $element->setRequired(true);
        $this->addElement($element);

        $element = new Zend_Form_Element_Text('phone');
        $element->setLabel('Phone:');
        $element->setRequired(true);
        $this->addElement($element);

        $element = new Zend_Form_Element_Text('region');
        $element->setLabel('Region:');
        $element->setRequired(true);
        $this->addElement($element);

        $element = new Zend_Form_Element_Text('city');
        $element->setLabel('City:');
        $element->setRequired(true);
        $this->addElement($element);

        $element = new Zend_Form_Element_Text('zip');
        $element->setLabel('ZIP:');
        $this->addElement($element);

        $element = new Zend_Form_Element_Text('address');
        $element->setLabel('Address:');
        $this->addElement($element);

        $element = new Zend_Form_Element_Text('mobile_code');
        $element->setLabel('Mobile Phone Code:');
        $this->addElement($element);

        $element = new Zend_Form_Element_Text('mobile_number');
        $element->setLabel('Mobile Phone Number:');
        $this->addElement($element);

        $submitElement = new Zend_Form_Element_Submit('submit');
        $submitElement->setIgnore(true);
        $this->addElement($submitElement);
    }

}