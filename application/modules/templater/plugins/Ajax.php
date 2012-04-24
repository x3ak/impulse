<?php

class Templater_Plugin_Ajax extends Zend_Controller_Plugin_Abstract
{

    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        parent::preDispatch($request);
    }

}