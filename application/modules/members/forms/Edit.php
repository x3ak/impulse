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
        $email->addValidator(new Zend_Validate_EmailAddress());
        $email->setRequired()->setAllowEmpty(false);
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
        $this->addElement($birthdate);

        $phone = new Zend_Form_Element_Text('phone');
        $phone->setLabel('phone');

        $this->addElement($phone);

        $save = new Zend_Form_Element_Submit('save');
        $save->setLabel('save');
        $this->addElement($save);

    }

    public function setDefaults(array $defaults)
    {
        if(empty($defaults['id'])) {
            $subscriptionType = new Zend_Form_Element_Select('subscription_type');

            $subscriptionType->setLabel('Subscription');

            $list = Members_Model_DbTable_SubscriptionType::getInstance()->findAll();
            foreach($list as $subscription) {
                $subscriptionType->addMultiOption($subscription->id, $subscription->title . ' - '.$subscription->price);
            }

            $subscriptionType->setOrder(2);
            $this->addElement($subscriptionType);


        } else {
            $identity = Zend_Auth::getInstance()->getIdentity();

            if($identity->role != 'ADMIN') {
                $number = new Zend_Form_Element_Hidden('number');
                $number->setLabel('number');
                $number->setRequired()->setAllowEmpty(false);
                $number->addValidator(new Zend_Validate_Int());
                $number->setDescription($defaults['number']);
                $this->addElement($number);


                $firstname = new Zend_Form_Element_Hidden('firstname');
                $firstname->setLabel('firstname');
                $firstname->setRequired()->setAllowEmpty(false);
                $firstname->addValidator(new Zend_Validate_Alnum());
                $firstname->setDescription($defaults['firstname']);
                $this->addElement($firstname);

                $firstname = new Zend_Form_Element_Hidden('lastname');
                $firstname->setLabel('lastname');
                $firstname->setRequired()->setAllowEmpty(false);
                $firstname->addValidator(new Zend_Validate_Alnum());
                $firstname->setDescription($defaults['lastname']);
                $this->addElement($firstname);

                $firstname = new Zend_Form_Element_Hidden('sex');
                $firstname->setLabel('sex');
                $firstname->setRequired()->setAllowEmpty(false);
                $firstname->addValidator(new Zend_Validate_Alnum());
                $firstname->setDescription($defaults['sex']);
                $this->addElement($firstname);
            }


        }
        return parent::setDefaults($defaults);
    }


}