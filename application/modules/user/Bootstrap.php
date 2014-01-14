<?php

class User_Bootstrap extends Zend_Application_Module_Bootstrap
{
    protected function _initAcl()
    {
        $this->bootstrap('Frontcontroller');

        $this->getResource('Frontcontroller')->registerPlugin(
            new User_Plugin_Acl()
        );

        $this->getResource('Frontcontroller')->registerPlugin(
            new User_Plugin_Navigation()
        );

    }
}