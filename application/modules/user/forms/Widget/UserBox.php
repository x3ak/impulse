<?php

/**
 * SlyS
 *
 * @author     Evgheni Poleacov <evgheni.poleacov@gmail.com>
 *
 * @version    $Id: UserBox.php 838 2010-12-21 10:54:03Z deeper $
 */
class User_Form_Widget_UserBox extends Zend_Form_SubForm
{

    public function init()
    {
        $element = new Zend_Dojo_Form_Element_FilteringSelect('box_type');
        $element->setLabel('Box type:');
        $element->addMultiOption('simple', 'Simple');
        $element->addMultiOption('detailed', 'Detailed');
        $element->setRequired(true);
        $this->addElement($element);
    }

}