<?php

/**
 * SlyS

 * @author     Evgheni Poleacov <evgheni.poleacov@gmail.com>
 *
 * @version    $Id: LinkButton.php 839 2010-12-21 10:54:20Z deeper $
 */

class Slys_Dojo_View_Helper_LinkButton extends Zend_Dojo_View_Helper_Button
{

    /**
     * Return button element HTML with link behavior
     *
     * @param string $id
     * @param string $value
     * @param array $params
     * @param array $attribs
     * @return string
     */
	public function linkButton($id, $value = null, array $params = array(), array $attribs = array())
	{
        if(!empty($params['url'])) {
            $redirector = 'window.location.href="'.$params['url'].'"';
            if(!empty($params['confirmMessage']))
                $params['onClick'] = 'if(confirm("'.$params['confirmMessage'].'")) '.$redirector;
            else
                $params['onClick'] = $redirector;
            unset($params['url']);
        }
        return $this-> button($id, $value, $params,$attribs);
	}
}