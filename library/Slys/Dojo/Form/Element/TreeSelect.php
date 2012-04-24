<?php

/**
 * Slys
 *
 * @author  Evgheni Poleacov <evgheni.poelacov@gmail.com>
 *          Pavel Galaton
 * @version $Id: TreeSelect.php 839 2010-12-21 10:54:20Z deeper $
 */
 class Slys_Dojo_Form_Element_TreeSelect extends Zend_Dojo_Form_Element_ComboBox
{
	/**
     * Use Tree view helper
     * @var string
     */
    public $helper = 'TreeSelect';

	protected $_multiple = false;

	protected $_checkedValue = '1';

	protected $_unCheckedValue = '0';

    /**
     * Switch tree to multiple select mode
     * @param boolean $multiple
     * @return Slys_Dojo_Form_Element_TreeSelect
     */
	public function setMultiple($multiple = true)
	{
		$this->setDijitParam('multiple',$multiple);
		$this->_multiple = $multiple;
	    return $this;
	}

    /**
     * Set dojo.data Store identificator
     *
     * @param string $identifier
     * @return string
     */
	public function  setStoreId($identifier) {
		$this->setMultiple($this->_multiple);
		return parent::setStoreId($identifier);
	}

    /**
     * Set tree nodes
     *
     * @param Zend_Dojo_Data $tree
     * @return Slys_Dojo_Form_Element_TreeSelect
     */
	public function setMultiOptions(Zend_Dojo_Data $tree)
	{
		$storeId = $this->getStoreId();
		if(empty($storeId))
			$storeId = 'treeWidgetId'.$this->getName();
		$this->setStoreId($storeId);
		$this->setStoreParams(array('data'=>$tree->toArray()));
		$this->setDijitParam('treeName', $this->getName());
		$this->setMultiple($this->_multiple);
		return $this;
	}

    /**
     * Set dijit.tree custom parameter
     *
     * @param string $key
     * @param mixed $value
     * @return Slys_Dojo_Form_Element_TreeSelect
     */
	public function setTreeParam($key, $value)
	{
		$params = $this->getTreeParams();
		$params[$key] = $value;
		$this->setTreeParams($params);
		return $this;
	}

    /**
     * Set dijit.tree custom multi params
     *
     * @param array $params
     * @return Slys_Dojo_Form_Element_TreeSelect
     */
	public function setTreeParams($params)
	{
		$this->setDijitParam('treeParams', $params);
		return $this;
	}

    /**
     * Return tree parameters
     *
     * @return array
     */
	public function getTreeParams()
	{
		$params = $this->getDijitParams();
		if(empty($params['treeParams']))
			return array();
		else
			return $params['treeParams'];
	}

    /**
     * Set tree nodes identities which should be selected
     *
     * @param mixed $value
     * @return Slys_Dojo_Form_Element_TreeSelect
     */
	public function setValue($value)
	{
		$this->setTreeParam('value', $value);
		return parent::setValue($value);
	}

    /**
     * Set value which should be assigned for checked tree node
     *
     * @param mixed $value
     */
	public function setCheckedValue($value)
	{

	}

    /**
     * Set disabled condition for every nod
     *
     * @param string $field
     * @param mixed $value
     * @return Slys_Dojo_Form_Element_TreeSelect
     */
    public function setDisabledField($field, $value = null)
    {
        $this->setTreeParam('disabledField', $field);
        if($value !== null)
            $this->setTreeParam('disabledValue', $value);
        return $this;
    }

    /**
     * Set root element properties
     *
     * @param string $label
     * @param mixed $value
     * @return Slys_Dojo_Form_Element_TreeSelect
     */
    public function setRoot($label, $value = null)
    {
        $this->setTreeParam('label','Root');
        if($value !== null)
            $this->setTreeParam('rootValue',0);
        return $this;
    }

    /**
     * Add options to indicated node
     * 
     * @param array $options
     * @param int $nodeId
     */
    public function addMultiOptions(Zend_Dojo_Data $options, $nodeId = null)
    {
        $storeParams = $this->getStoreParams();
        if(empty($storeParams['data']))
            $this->setMultiOptions($options);
        elseif(!empty($nodeId)) {
            $items = $this->_appendToNode($this->_resetArrayKeys(
                $storeParams['data']['items']),
                $this->_resetArrayKeys($options->getItems()),
                $options->getIdentifier(),
                $nodeId
            );
            $options->setItems($items);
            $this->setMultiOptions($options);
        } else {
          $options->setItems(array_merge(
              $storeParams['data']['items'], $options->getItems()));
          $this->setMultiOptions($options);
        }

    }

    /**
     * Append new options to indicated node
     *
     * @param array $options
     * @param array $newOptions
     * @param string $rkey
     * @param midex $rvalue
     * @return array
     */
    protected function _appendToNode($options, $newOptions, $rkey, $rvalue)
    {
        $return = array();
        foreach ($options as $key => $value) {
            if($key === $rkey && $value == $rvalue ) {
                if(!empty($options['__children']) && is_array($options['__children']))
                    $return['__children'] = array_merge ($return['__children'], $newOptions);
                elseif (!isset($options['__children'])) {
                    $return['__children'] = $newOptions;
                }
            }
            if (is_array($value))
                $value = $this->_appendToNode($value, $newOptions, $rkey, $rvalue);
                if(is_numeric($key))
                    $return[] = $value;
                else
                    $return[$key] = $value;
            
        }
        krsort($return);
        return $return;
    }

    /**
     * Reset array keys
     *
     * @param array $options
     * @return array
     */
    protected function _resetArrayKeys($options)
    {
        $return = array();
        foreach ($options as $key => $value) {

            if (is_array($value))
                $value = $this->_resetArrayKeys($value);

            if(is_numeric($key))
                $return[] = $value;
            else
                $return[$key] = $value;
        }
        return $return;
    }
}
