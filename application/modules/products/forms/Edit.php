<?php
/**
 * Author: Pavel
 * $Id:$
 */

class Products_Form_Edit extends Zend_Form
{
    public function init()
    {
        $this->setMethod(self::METHOD_POST);

        $id = new Zend_Form_Element_Hidden('id');
        $id->removeDecorator('HtmlTag');
        $id->removeDecorator('Label');
        $this->addElement($id);

        $number = new Zend_Form_Element_Text('title');
        $number->setLabel('title');
        $number->setRequired()->setAllowEmpty(false);
        $this->addElement($number);

        $price = new Zend_Form_Element_Text('price');
        $price->setLabel('price');
        $this->addElement($price);

        $price = new Zend_Form_Element_Text('amount');
        $price->setLabel('amount');
        $price->setRequired()->setAllowEmpty(false);
        $price->setDescription('Please specify how much of this product you have');
        $this->addElement($price);

        $active = new Zend_Form_Element_Checkbox('active');
        $active->setLabel('Active');
        $active->setValue(true);
        $this->addElement($active);

        $save = new Zend_Form_Element_Submit('save');
        $save->setLabel('save');
        $this->addElement($save);

    }
}