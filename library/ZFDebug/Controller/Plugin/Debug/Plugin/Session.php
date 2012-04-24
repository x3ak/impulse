<?php
class ZFDebug_Controller_Plugin_Debug_Plugin_Session extends ZFDebug_Controller_Plugin_Debug_Plugin
													 implements ZFDebug_Controller_Plugin_Debug_Plugin_Interface {
    protected $_identifier = 'session';

    public function __construct() {
    }

    public function getIdentifier() {
        return $this->_identifier;
    }

    public function getTab() {
        $html = "Session";
        return $html;
    }

    public function getPanel() {
        $html = '<h4>Session</h4>';

        if (!empty($_SESSION)) {
        	ob_start();
        	$html .= print_r($_SESSION, true);
        	ob_end_clean();
        }
        else
        	$html .= 'Session is empty';

        return $html;
    }
}