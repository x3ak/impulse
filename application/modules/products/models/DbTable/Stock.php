<?php

/**
 * SlyS
 *
 * This is a class generated with Zend_CodeGenerator.
 *
 * @version $Id: Generator.php 1158 2011-02-02 09:48:32Z deeper $
 * @license New BSD
 */
class Products_Model_DbTable_Stock extends Doctrine_Table
{

    /**
     * Returns an instance of this class.
     *
     * @return Products_Model_DbTable_Stock
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('Products_Model_Mapper_Stock');
    }


}

