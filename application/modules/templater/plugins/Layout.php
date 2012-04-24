<?php

/**
 * Slys
 *
 * Template layout switcher. Used to check and switch layout for the current theme
 * such file exists
 *
 * @author Serghei Ilin <criolit@gmail.com>
 */
class Templater_Plugin_Layout extends Zend_Controller_Plugin_Abstract
{

    /**
     * Templater options
     * @var array
     */
    protected $_options;
    /**
     * Layout object
     *
     * @var Zend_Layout
     */
    protected $_layout;

    /**
     * Constructor
     * @param array $options
     */
    public function __construct($options = array())
    {
        $this->setOptions($options);
    }

    /**
     * Set templater options
     * @param array $options
     * @return Templater_Plugin_Layout
     */
    public function setOptions($options = array())
    {
        $this->_options = $options;
        return $this;
    }

    /**
     * Get Tempalter options
     * @return array
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * On dispatch loop startup layout change is happens
     *
     * @param Zend_Controller_Request_Abstract $request
     *
     * @return void
     */
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        $config = new Zend_Config($this->getOptions());
        $themeSettings = $config->toArray();
        $apiRequest = new Slys_Api_Request($this, 'sysmap.currently-active-items');
        $mapIdentifiers = $apiRequest->proceed()->getResponse()->getFirst();

//        Zend_Debug::dump($mapIdentifiers->toArray());
        /**
         * Get current layout from config
         */
        if(empty($mapIdentifiers)) {
            $currentLayout = Templater_Model_DbTable_Layout::getInstance()->getDefaultLayout();
        } else {
            $currentLayout = Templater_Model_DbTable_Layout::getInstance()
                                ->getCurrentLayout($mapIdentifiers);
        }

        $frontController = Zend_Controller_Front::getInstance();


        /**
         * Set current layout
         */

        if (!$frontController->hasPlugin('Zend_Layout_Controller_Plugin_Layout')) {
            $frontController->getParam('bootstrap')
                ->registerPluginResource('layout')
                ->bootstrap('layout');
        }

        $this->_layout = $frontController
            ->getPlugin('Zend_Layout_Controller_Plugin_Layout')
            ->getLayout();

        if (empty($currentLayout)) {
            $this->_layout->disableLayout();
            throw new Zend_Exception('No active layouts for theme found or no active theme found');
        }

        $layoutPath = $config->directory . DIRECTORY_SEPARATOR .
                $currentLayout->Theme->name . DIRECTORY_SEPARATOR .
                $themeSettings['layout']['directory'];

        $layoutName = $currentLayout->name;
        $layoutFile = realpath($layoutPath . DIRECTORY_SEPARATOR . $layoutName . '.phtml');

        if (file_exists($layoutFile)) {
            $this->_layout->setLayoutPath($layoutPath);
            $this->_layout->setLayout($layoutName);

            $frontController->setParam('noErrorHandler', true);
            $frontController->registerPlugin(new Templater_Plugin_ErrorHandler(), 98);

            if(!$request->isXmlHttpRequest())
                Zend_Controller_Action_HelperBroker::addHelper(
                    new Templater_Library_Controller_Action_Helper_Widget($this->getOptions()));
        } else {
            throw new Zend_Exception('Layout "' . $layoutPath .
                    DIRECTORY_SEPARATOR . $layoutName . '" established for this page not found');
        }
    }

}