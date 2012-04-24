<?php

class Slys_Dojo_Form_Decorator_Container extends Zend_Form_Decorator_Abstract
{
	static public $_content = array();
	protected $_current;
	protected $_helper = false;
	protected $_title = null;


	public function __construct($name = null, $helper = false, $options = null)
	{
		
		$this->_helper = $helper;
		if(!empty($name))
			$this->_current = $name;

		if(!empty($options['title']))
			$this->_title = $options['title'];
			
		parent::__construct($options);
	}
	
    public function render($content)
	{
		self::$_content[$this->_current][] = $content;
		
		if($this->_helper) {
			$decoratorName = $this->_helper[0]; 
			$params = $this->_helper[1];
			$decoratorClass = $this->getElement()->getPluginLoader('decorator')->load($decoratorName);
			$decorator = new $decoratorClass($params);
			$decorator->setElement($this->getElement());
			if(!empty($this->_title))
				array_unshift(self::$_content[$this->_current], '<div class="containerTitle">'.$this->_title.'</div>');
			$output = $decorator->render(implode('',self::$_content[$this->_current]));
			
			self::$_content = array();
			$this->_helper = false;
			return $output; 
		}

	}

	public function setTitle($title)
	{
		$this->_title = $title;
		return $this;
	}

	public function getTitle()
	{
		return $this->_title;
	}
}
