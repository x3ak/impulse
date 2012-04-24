<?php

class Slys_Dojo_View_Helper_TitlePane extends Zend_Dojo_View_Helper_DijitContainer
{
    /**
     * Dijit being used
     * @var string
     */
    protected $_dijit  = 'dijit.TitlePane';

    /**
     * Module being used
     * @var string
     */
    protected $_module = 'dijit.TitlePane';

    /**
     * dijit.layout.ContentPane
     *
     * @param  string $id
     * @param  string $content
     * @param  array $params  Parameters to use for dijit creation
     * @param  array $attribs HTML attributes
     * @return string
     */
    public function titlePane($id = null, $content = '', array $params = array(), array $attribs = array())
    {
		if(!isset($params['open']))
			$params['open'] = false;

        if (0 === func_num_args()) {
            return $this;
        }

        return $this->_createLayoutContainer($id, $content, $params, $attribs);
    }
}
