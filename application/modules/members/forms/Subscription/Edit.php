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

        $enterTime = new Zend_Form_Element_Text('start_date');
        $enterTime->setLabel('start_date');
        $this->addElement($enterTime);

        $exitTime = new Zend_Form_Element_Text('expire_date');
        $exitTime->setLabel('expire_date');
        $this->addElement($exitTime);


        $save = new Zend_Form_Element_Submit('save');
        $save->setLabel('save');
        $this->addElement($save);


    }
}