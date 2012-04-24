<?php
/**
 * Created by JetBrains PhpStorm.
 * User: criollit
 * Date: 17.01.11
 * Time: 12:15
 * To change this template use File | Settings | File Templates.
 */

class Slys_Api_Request
{
    /**
     * @var Object which called the request (can be null)
     */
    protected $_context = null;
    /**
     * @var string
     */
    protected $_contextModuleName = null;
    /**
     * @var string Name of the request
     */
    protected $_name = 'slys.request';
    /**
     * @var array additionals parameters of the request
     */
    protected $_params = array();
    /**
     * @var Slys_Api_Request_Response
     */
    protected $_response = null;

    /**
     * @param  Object $context
     * @param  string|object $name
     * @param  array $params
     * @param  null|Zend_Validate_Callback $filter
     *
     */
    public function __construct($context, $name, $params = array())
    {
        if ($context === null)
            throw new Zend_Exception('Context must be an instance of the sender object or the name of the existing module!');

        if (is_object($context)) {
            $className = explode('_', get_class($context), 2);
            $this->_contextModuleName = strtolower($className[0]);
        }
        elseif(is_string($context))
            $this->_contextModuleName = strtolower($context);

        if (Zend_Controller_Front::getInstance()->getControllerDirectory($this->_contextModuleName) === null)
            throw new Zend_Exception('Context object class must belong to one of the registered application module!');

        $this->_context = $context;
        $this->_name = strtolower(trim($name));
        $this->_params = $params;
        $this->_response = new Slys_Api_Request_Response($this);
    }

    /**
     * Returns the name of the request
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Returns request parameters
     * @return array
     */
    public function getParams()
    {
        return $this->_params;
    }

    /**
     * Execute
     * @return Slys_Api_Request
     */
    public function proceed($params = array())
    {
        if (empty($params) === false)
            $this->_params = $params;

        $this->_response = new Slys_Api_Request_Response($this);
        Slys_Api::getInstance()->request($this);
        return $this;
    }

    /**
     * Returns object which contains responses from handlers
     * @return Slys_Api_Request_Response
     */
    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * Returns caller object if it was passed
     * @return null|Object
     */
    public function getContext()
    {
        return $this->_context;
    }

    /**
     * @return null|string
     */
    public function getContextModuleName()
    {
        return $this->_contextModuleName;
    }
}