<?php

/**
 * SlyS
 *
 * @author     Evgheni Poleacov <evgheni.poleacov@gmail.com>
 *
 * @version    $Id: FlashMessager.php 269 2010-10-05 13:38:46Z deeper $
 */

class Templater_Form_FlashMessager extends Zend_Dojo_Form
{
	public function  init()
	{
		$this->setDecorators(array('Description', 'FormElements','Errors'));

		$element = new Zend_Dojo_Form_Element_TextBox('type_id');
		$element->setLabel('Type:');
		$element->setRequired(true);
		$this->addElement($element);

		$element = new Zend_Dojo_Form_Element_RadioButton('current');
		$element->setSeparator('&nbsp;');
		$element->setLabel('Current:');
		$element->setMultiOptions(array('1'=>'Yes','0'=>'No'));
		$element->setRequired(true);
		$this->addElement($element);

		$this->setDescription('Flash messages parameters:');
	}
}