<?php

/**
 * SlyS
 * 
 * This is a class generated with Zend_CodeGenerator.
 * 
 * @version $Id: Item.php 269 2010-10-05 13:38:46Z deeper $
 * @license New BSD
 */
class Navigation_Model_DbTable_Item extends Doctrine_Table
{

    /**
     * Returns an instance of this class.
     * 
     * @return Navigation_Model_DbTable_Item
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('Navigation_Model_Mapper_Item');
    }


}

