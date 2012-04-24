<?php

/**
 * SlyS
 * 
 * This is a class generated with Zend_CodeGenerator.
 * 
 * @version $Id: Generator.php 1158 2011-02-02 09:48:32Z deeper $
 * @license New BSD
 */
class Sysmap_Model_Mapper_Sysmap extends Sysmap_Model_Mapper_BaseSysmap
{
	public function getMapIdentifier()
    {
        return $this->hash;
    }

    /**
     * Return current MCA as a request object
     * @return Zend_Controller_Request_Simple
     */
    public function toRequest()
    {
        if (empty($this->mca) and $this->level == 4)
            $details = Sysmap_Model_Map::getInstance()->parseMcaFormat($this->getNode()->getParent()->mca);
        else
            $details = Sysmap_Model_Map::getInstance()->parseMcaFormat($this->mca);

        return new Zend_Controller_Request_Simple($details['action'], $details['controller'], $details['module'], (array)$this->params);
    }
}