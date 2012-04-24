<?php
/**
 * Slys
 *
 * Theme support for the Slys applications
 *
 * @author Evgheni Poleacov <evgheni.poleacov@gmail.com>
 */

class Templater_Bootstrap extends Zend_Application_Module_Bootstrap implements Slys_Api_Request_Requestable
{
    /**
     * Tempalter plugins initialization
     */
    protected function _initPlugins()
    {
        $plugin = new Templater_Plugin_Layout($this->getOptions());
        
        Zend_Controller_Front::getInstance()
            ->registerPlugin($plugin);
    }

    public function onRequest(Slys_Api_Request $request)
    {
        switch ($request->getName()) {
            case 'navigation.get-module-navigation':
                $types = $this->getResourceLoader()->getResourceTypes();
                $navigationPath = $types['config']['path'].DIRECTORY_SEPARATOR.'navigation.yml';
                if(is_file($navigationPath)) {
                    $navigation = new Zend_Navigation_Page_Mvc(new Zend_Config_Yaml($navigationPath));
                    $request->getResponse()->setData($navigation);
                }
            break;
        }
    }

}