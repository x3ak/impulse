<?php

/**
 * Slys
 *
 * @author     Evgheni Poleacov <evgheni.poleacov@gmail.com>
 * @version    $Id: StripArrayObjectKey.php 839 2010-12-21 10:54:20Z deeper $
 */

class Slys_Filter_StripArrayObjectKey implements Zend_Filter_Interface
{
	/**
	 * ArrayObject key
	 * 
	 * @var string
	 */
	protected $_key;

	/**
	 * Flag which indicate if found key should be removed
	 *
	 * @var boolean
	 */
	protected $_remove;

	/**
	 * Flag for key found event
	 *
	 * @var boolean
	 */
	protected $_found = false;

		/**
	 * Filter constructor
	 * 
	 * @param string $key 
	 */
	public function  __construct($key = '')
	{
		$this->_key = $key;
	}

	public function setRemove($flag = true)
	{
		$this->_remove = $flag;
		return $this;
	}

	/**
	 * Search ArrayObject keys
	 *
	 * @param array $input
	 * @return boolean
	 */
	public function filter($input)
	{
		if(is_array($input))
			$input = new ArrayObject($input);
		foreach($input as $key=>$value) {
			if($key == $this->_key) {
				$this->_found = true;
				if($this->_remove)
					unset($input[$key]);
				if($value instanceof Iterator) {
					$this->_found = $this->filter($value);
				}
			}
		}

		return $this->_found;
	}
}
