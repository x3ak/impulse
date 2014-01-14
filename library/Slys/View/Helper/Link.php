<?php
/**
 * SlyS Framework
 *
 * @category   SlyS
 * @package    Slys_View
 * @subpackage Helper
 * @version    $v$
 */
class Slys_View_Helper_Link extends Zend_View_Helper_HtmlElement
{
	public function link($title, $url, array $additional = null,$confirmMessage = null)
	{
        $identity = Zend_Auth::getInstance()->getIdentity();

        if(!empty($identity) && $identity->role != 'ADMIN') {
            /** @var $defaultRoute Zend_Controller_Router_Route_Module */
            $defaultRoute = Zend_Controller_Front::getInstance()->getRouter()->getRoute('admin');

            $data = $defaultRoute->match($url);

            if(empty($data)) {
                $adminRoute = Zend_Controller_Front::getInstance()->getRouter()->getRoute('default');
                $data = $adminRoute->match($url);
            }



            $mca = implode('.', array($data['module'], $data['controller'], $data['action']));
            $acl = Zend_Registry::get('ACL');


            if(!in_array(strtolower($mca), $acl[$identity->role])) {
                return '';
            }
        }

        //TODO add ACL
        $title = $this->view->translate($title);
		$attribs = array('href' => $url);
		if($additional !== null)
			$attribs += $additional;

		if(!is_null($confirmMessage)) {
            $confirmMessage = $this->view->translate($confirmMessage);
			$filter = new Zend_Filter_HtmlEntities();
			$filter->setQuoteStyle(ENT_QUOTES);
			$confirmMessage = $filter->filter(str_replace("'","\'",$confirmMessage));
			$attribs['onclick'] = "return confirm('".$confirmMessage."');";
		}

		$attribs_string = $this->_htmlAttribs($attribs);
		return "<a".$attribs_string.">$title</a>";
	}
}