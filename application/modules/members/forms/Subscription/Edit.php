<?php
/**
 * Author: Pavel
 * $Id:$
 */

class Members_Form_Subscription_Edit extends Zend_Form
{
    public function init()
    {
        $this->setMethod(self::METHOD_POST);

        $id = new Zend_Form_Element_Hidden('id');
        $id->removeDecorator('HtmlTag');
        $id->removeDecorator('Label');
        $this->addElement($id);

        $title = new Zend_Form_Element_Text('title');
        $title->setLabel('title');
        $title->setRequired()->setAllowEmpty(false);
        $this->addElement($title);

        $description = new Zend_Form_Element_Text('description');
        $description->setLabel('description');
        $description->setRequired(false)->setAllowEmpty(true);
        $this->addElement($description);

        $duration = new Zend_Form_Element_Text('duration');
        $duration->setLabel('duration');
        $duration->addValidator(new Zend_Validate_Int());
        $this->addElement($duration);

        $units = new Zend_Form_Element_Select('units');

        $units->addMultiOption('HOURS','HOURS');
        $units->addMultiOption('DAYS','DAYS');
        $units->addMultiOption('WEEKS','WEEKS');
        $units->addMultiOption('MONTHS','MONTHS');
        $units->addMultiOption('YEARS','YEARS');


        $units->setLabel('units');
        $this->addElement($units);

        $enterTime = new Zend_Form_Element_Text('enter_time');
        $enterTime->setLabel('enter_time');
        $enterTime->setAttrib('placeholder','Unlimited');
        $this->addElement($enterTime);

        $exitTime = new Zend_Form_Element_Text('exit_time');
        $exitTime->setLabel('exit_time');
        $exitTime->setAttrib('placeholder','Unlimited');
        $this->addElement($exitTime);

        $visitsPerWeek = new Zend_Form_Element_Text('visits_per_week');
        $visitsPerWeek->setLabel('visits_per_week');
        $visitsPerWeek->setAttrib('placeholder','Unlimited');
        $visitsPerWeek->addValidator(new Zend_Validate_Int());
        $this->addElement($visitsPerWeek);

        $price = new Zend_Form_Element_Text('price');
        $price->setLabel('price');
        $this->addElement($price);



        $save = new Zend_Form_Element_Submit('save');
        $save->setLabel('save');
        $this->addElement($save);


    }
}