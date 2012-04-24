<?php

/**
 * Display layout widgets according with block list stored in database
 *
 * @author deeper
 */
class Templater_Library_Controller_Action_Helper_Widget extends Zend_Controller_Action_Helper_Abstract
{

    /**
     * Blocks action marker
     * @var string
     */
    private $_marker = '_responseSegment';
    /**
     * Application action stack
     * @var Zend_Controller_Plugin_ActionStack
     */
    private $_stack = null;
    static protected $_started = false;
    protected $_incomingWidgetId = null;
    protected $_POST = array();
    protected $_widgetIdMarkerName = null;
    protected $_widgetIdName = '_widgetInternalId';
    protected $_widgetPostMarker = '_widgetId';
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

    public function init()
    {
        $this->_front = Zend_Controller_Front::getInstance();
        if ($this->_front->hasPlugin('Zend_Layout_Controller_Plugin_Layout'))
            $this->_layout = $this->_front
                                ->getPlugin('Zend_Layout_Controller_Plugin_Layout')
                                ->getLayout();
    }

    /**
     * Switch viewRenderer responseSegment to
     * segment received from request
     */
    public function preDispatch()
    {
        /**
         * Custom action views placed in themes 
         */
        $viewRenderer = Zend_Controller_Action_HelperBroker::getExistingHelper('viewRenderer');
        $module = $this->getRequest()->getModuleName();
        $options = $this->getOptions();
        $viewPath = realpath($this->_layout->getLayoutPath() . '/../' .
                $options['view']['directory'] . '/' . $module . '/scripts/');

        if ($viewPath && !in_array($viewPath, $this->_layout->getView()->getScriptPaths())) {
            $this->_layout->getView()->addScriptPath($viewPath);
        }

        /**
         * Set widget markers into the new form parameters
         */
        $widgetId = $this->getRequest()->getParam($this->_widgetIdName);
        if($widgetId !== null) {
            $formHelper = $this->_layout->getView()->getHelper('form');
            if($formHelper instanceof Slys_View_Helper_Form)
                $formHelper->setMarker($this->_widgetPostMarker, $widgetId);
        }

        /**
         * Check post parameters
         */
        $postFormMarker = $this->getRequest()->getParam($this->_widgetPostMarker);

        if($this->getRequest()->isPost()) { 
            if ($postFormMarker !== null && $widgetId === null) {
                //id not widget request and post marker found
                $_SERVER['REQUEST_METHOD'] = 'GET';
                $this->_POST = $_POST;
            } elseif($widgetId !== $postFormMarker) {
                //if post marker NOT of current widget request
                $_SERVER['REQUEST_METHOD'] = 'GET';
                $this->_POST = $_POST;
            }
        }

        if ($postFormMarker !== null && $widgetId == $postFormMarker && !empty($this->_POST)) {            
            $_SERVER['REQUEST_METHOD'] = 'POST';
            $_POST = $this->_POST;
            $this->_POST = null;
        }
        
        /**
         * Set view renderer segment of current widget
         */
        $slot = $this->getRequest()->getParam($this->_marker, null);
        $viewRenderer->setResponseSegment($slot);
    }

    /**
     * Find and add application widgets for current request
     */
    public function postDispatch()
    {

        if (!self::$_started) {
            self::$_started = true;

            $front = $this->_front;
            $layout = $this->_layout;

            if ($layout->isEnabled()) {
                $this->_initStack();

                $apiRequest = new Slys_Api_Request($this, 'sysmap.currently-active-items');
                $mapIdentifiers = $apiRequest->proceed()->getResponse()->getFirst();

                $request = $this->getActionController()->getRequest();
                $layoutEntity = Templater_Model_DbTable_Widget::getInstance()
                                ->getLayoutWithWidgetsbyNameAndRequest($layout->getLayout(), $mapIdentifiers);

                $apiRequest = new Slys_Api_Request($this, 'user.get-acl');
                $acl = $apiRequest->proceed()->getResponse()->getFirst();

                if (!empty($layoutEntity->Widgets))
                    foreach ($layoutEntity->Widgets as $widget) {

                        if (!$acl instanceof Zend_Acl
                                || !$acl->isAllowed(null, $widget->map_id)
                                        || !$widget->published) {
                            continue;
                        }
                        $apiRequest = new Slys_Api_Request($this, 'sysmap.get-item-by-identifier', 
                                                            array('identifier'=>$widget->map_id));
                        $mapItem = $apiRequest->proceed()->getResponse()->getFirst();
                        if(!$mapItem instanceof Sysmap_Model_Mapper_Sysmap)
                            continue;
                        $widgetRequest = $mapItem->toRequest();

                        $this->_pushStack($widget->id, $widgetRequest, $widget->placeholder,
                                            (array) $widgetRequest->getParams());
                    }
            }
        }
    }

    /**
     * Push actions into application actions stack
     * @param int $id
     * @param Templater_Api_Interface_Widget $widget
     * @param string $placeholder
     * @param array $params 
     */
    protected function _pushStack($id, Zend_Controller_Request_Abstract $widget, $placeholder, $params = array())
    {
        $params[$this->_widgetIdName] = md5($id);
        $camelFilter = new Zend_Filter_Word_CamelCaseToDash('-');
        $blockRequest = new Zend_Controller_Request_Simple(
                        strtolower($camelFilter->filter($widget->getActionName())),
                        strtolower($camelFilter->filter($widget->getControllerName())),
                        strtolower($camelFilter->filter($widget->getModuleName())),
                        array_merge($params, array($this->_marker => $placeholder))
        );

        $this->_stack->pushStack($blockRequest);
    }

    /**
     *
     * @return Zend_Controller_Plugin_ActionStack
     */
    protected function _initStack()
    {
        if (null === $this->_stack) {
            $front = Zend_Controller_Front::getInstance();
            if (!$front->hasPlugin('Zend_Controller_Plugin_ActionStack')) {
                $stack = new Zend_Controller_Plugin_ActionStack();
                $front->registerPlugin($stack);
            } else {
                $stack = $front->getPlugin('ActionStack');
            }
            $this->_stack = $stack;
        }
        return $this->_stack;
    }
}