<?php
/**
 * Hausmed
 *
 * @filesource  /library/HM/Acl/Assertion/Params.php
 * 
 * @author      Evgheni Poleacov <evgheni.poleacov@hausmed.de> *
 * @version     $Id: Params.php 269 2010-10-05 13:38:46Z deeper $
 */

class User_Library_Acl_Assertion_Params implements Zend_Acl_Assert_Interface {
	
	protected $_params = array();
	protected $_rule;
	
	public function __construct($params = array(), $rule = 'allow') {
		$this->_params = $params;
	}
	
	public function assert(Zend_Acl $acl, Zend_Acl_Role_Interface $role = null, Zend_Acl_Resource_Interface $resource = null, $privilege = null) {
		$request_params = Zend_Controller_Front::getInstance()->getRequest()->getParams();

		$true = false;
		if(!empty($this->_params)){
			foreach($this->_params as $name=>$value){
				if(isset($request_params[$name]) && $request_params[$name] == $value)
					$true = true;
				else 
					$true = false;
					
			}
		}

    	return $true;                	
    }
    
}