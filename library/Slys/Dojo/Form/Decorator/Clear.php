<?php

class Slys_Dojo_Form_Decorator_Clear extends Zend_Form_Decorator_Abstract
{
    public function render($content)
	{
		$content .= '<div style="clear:both;"></div>';
		return $content;
	}
}
