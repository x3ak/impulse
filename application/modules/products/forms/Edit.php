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

        $save = new Zend_Form_Element_Submit('save');
        $save->setLabel('save');
        $this->addElement($save);

    }
}