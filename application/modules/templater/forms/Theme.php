<?php

/**
 * SlyS
 *
 * @author     Evgheni Poleacov <evgheni.poleacov@gmail.com>
 *
 * @version    $Id: Theme.php 867 2010-12-22 12:44:26Z deeper $
 */
class Templater_Form_Theme extends Zend_Dojo_Form
{

    public function init()
    {
        $this->setMethod('POST');
        $element = new Zend_Dojo_Form_Element_TextBox('title');
        $element->setLabel('Title:');
        $element->setRequired(true);
        $this->addElement($element);

        $element = new Zend_Dojo_Form_Element_FilteringSelect('name');
        $element->setLabel('Directory:');
        $element->setRequired(true);
        $this->addElement($element);

        $element = new Zend_Dojo_Form_Element_RadioButton('current');
        $element->setSeparator('&nbsp;');
        $element->setLabel('Current:');
        $element->setValue(false);
        $element->setMultiOptions(array('1' => 'Yes', '0' => 'No'));
        $element->setRequired(true);
        $this->addElement($element);

        $element = new Zend_Dojo_Form_Element_RadioButton('import_layouts');
        $element->setSeparator('&nbsp;');
        $element->setLabel('Import layouts:');
        $element->setValue(false);
        $element->setMultiOptions(array('1' => 'Yes', '0' => 'No'));
        $this->addElement($element);

        $element = new Zend_Dojo_Form_Element_SubmitButton('submit');
        $element->setLabel('Save');
        $element->setIgnore(true);
        $this->addElement($element);
    }

}