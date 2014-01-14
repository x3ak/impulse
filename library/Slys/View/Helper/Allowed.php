<?php
class Slys_View_Helper_Allowed extends Zend_View_Helper_HtmlElement
{
	public function allowed($mca)
	{
        $identity = Zend_Auth::getInstance()->getIdentity();

        if(!empty($identity) && $identity->role != 'ADMIN') {
            $acl = Zend_Registry::get('ACL');


            return in_array(strtolower($mca), $acl[$identity->role]);
        }

        return true;

	}
}