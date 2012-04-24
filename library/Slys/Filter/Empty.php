<?php

/**
 * Slys
 *
 * @author     Evgheni Poleacov <evgheni.poleacov@gmail.com>
 * @version    $Id: Empty.php 839 2010-12-21 10:54:20Z deeper $
 */

class Slys_Filter_Empty implements Zend_Filter_Interface
{
	/**
	 * Unset empty values from array recursively
	 *
	 * @param array $input
	 * @return boolean
	 */
	public function filter($input)
	{
		if(is_array($input)) {
			foreach ($input as &$value)
			{
			  if (is_array($value))
			  {
				$value = $this->filter($value);
			  }
			}

			return array_filter($input);
		} else {
			//TODO: Create support of object filtering
			return $input;
		}
	}
}
