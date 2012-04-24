<?php
/**
 * Created by JetBrains PhpStorm.
 * User: criollit
 * Date: 17.01.11
 * Time: 11:23
 * To change this template use File | Settings | File Templates.
 */

class Slys_Api_Request_Response
{
    /**
     * @var array
     */
    protected $_data = array();
    /**
     * @var Slys_Api_Request
     */
    protected $_request = null;
    /**
     * @var Zend_Validate_Callback
     */
    protected $_filter;

    public function __construct(Slys_Api_Request $request)
    {
        $this->_request = $request;
    }

    /**
     * @param null|Zend_Validate_Callback $filter
     * @return array
     */
    public function getData(Zend_Validate_Callback $filter = null)
    {
        if ($filter !== null) {
            $resultData = array();

            foreach($this->_data as $value)
                if ($filter->isValid($value))
                    $resultData[] = $value;

            return $resultData;
        }

        return $this->_data;
    }

    public function getFirst(Zend_Validate_Callback $filter = null)
    {
        if (empty($this->_data))
            return null;

        if ($filter !== null) {
            $result = $this->getResponseArray($filter);

            if (empty($result) === true)
                return null;

            return current($result);
        }

        return current($this->_data);
    }

    /**
     * Set's the response of the current handler
     * @param  mixed $data
     * @param  int $priority
     * @return void
     */
    public function setData($data, $priority = 0)
    {
        if (Slys_Api::getInstance()->isRequestInProcess($this->_request->getName())) {
            $this->_data['data'] = $data;
            $this->_data['priority'] = (int)$priority;
        }
        else
            $this->_data = $data;
    }

    /**
     * @return Slys_Api_Request
     */
    public function getRequest()
    {
        return $this->_request;
    }
}