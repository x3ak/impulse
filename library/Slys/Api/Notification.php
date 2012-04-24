<?php
/**
 * Created by JetBrains PhpStorm.
 * User: criollit
 * Date: 17.01.11
 * Time: 12:18
 * To change this template use File | Settings | File Templates.
 */

class Slys_Api_Notification
{
    /**
     * @var string Name of the notification
     */
    protected $_name;
    /**
     * @var null|Object Notification creator object or null
     */
    protected $_context;
    /**
     * @var array Notification parameters
     */
    protected $_params;

    /**
     * @param  null|Object $context
     * @param  string $name
     * @param  array $params
     *
     */
    public function __construct($context, $name, $params = array())
    {
        $this->_context = $context;
        $this->_name = $name;
        $this->_params = $params;
    }

    /**
     * Get notification name
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Get notification parameters (if were set)
     * @return array
     */
    public function getParams()
    {
        return $this->_params;
    }

    /**
     * Get the raiser of the notification (if was set)
     * @return null|Object
     */
    public function getContext()
    {
        return $this->_context;
    }
}