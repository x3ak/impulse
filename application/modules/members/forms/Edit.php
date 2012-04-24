<?php
/**
 * Author: Pavel
 * $Id:$
 */

class Members_Form_Edit extends Zend_Form
{
    public function init()
    {
        $this->setMethod(self::METHOD_POST);

        $id = new Zend_Form_Element_Hidden('id');
        $id->removeDecorator('HtmlTag');
        $id->removeDecorator('Label');
        $this->addElement($id);

        $number = new Zend_Form_Element_Text('number');
        $number->setLabel('number');
        $number->setRequired()->setAllowEmpty(false);
        $number->addValidator(new Zend_Validate_Int());
        $this->addElement($number);

        $email = new Zend_Form_Element_Text('email');
        $email->setLabel('email');
        $email->setRequired(false)->setAllowEmpty(true);
        $email->addValidator(new Zend_Validate_EmailAddress());
        $this->addElement($email);

        $photo = new Zend_Form_Element_File('photo');
        $photo->setLabel('photo');
        $photo->setRequired(false)->setAllowEmpty(true);
        $photo->setDestination(APPLICATION_PATH.'/../public/photos/');
        $photo->addValidator('Count', false, 1);
        $photo->addValidator('Extension', false, 'jpg,png,gif');

        $this->addElement($photo);

        $sex = new Zend_Form_Element_Select('sex');
        $sex->setLabel('sex');
        $sex->addMultiOption('MALE', 'male');
        $sex->addMultiOption('FEMALE', 'female');
        $sex->setValue('MALE');
        $this->addElement($sex);

        $firstname = new Zend_Form_Element_Text('firstname');
        $firstname->setLabel('firstname');
        $firstname->setRequired()->setAllowEmpty(false);
        $firstname->addValidator(new Zend_Validate_Alnum());
        $this->addElement($firstname);

        $firstname = new Zend_Form_Element_Text('lastname');
        $firstname->setLabel('lastname');
        $firstname->setRequired()->setAllowEmpty(false);
        $firstname->addValidator(new Zend_Validate_Alnum());
        $this->addElement($firstname);

        $birthdate = new Zend_Form_Element_Text('birth_date');
        $birthdate->setLabel('birth_date');
        $birthdate->setRequired()->setAllowEmpty(false);
        $this->addElement($birthdate);

        $phone = new Zend_Form_Element_Text('phone');
        $phone->setLabel('phone');
        $phone->setRequired(false)->setAllowEmpty(true);
        $this->addElement($phone);

        $save = new Zend_Form_Element_Submit('save');
        $save->setLabel('save');
        $this->addElement($save);

    }
}