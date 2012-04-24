<?php
/**
 * Author: Pavel
 * $Id:$
 */

class Products_Form_Stock extends Zend_Form
{
    public function init()
    {
        $this->setMethod(self::METHOD_POST);

        $id = new Zend_Form_Element_Hidden('id');
        $id->removeDecorator('HtmlTag');
        $id->removeDecorator('Label');
        $this->addElement($id);

        $price = new Zend_Form_Element_Text('amount');
        $price->setLabel('amount');
        $this->addElement($price);

        $save = new Zend_Form_Element_Submit('save');
        $save->setLabel('save');
        $this->addElement($save);

    }
}