<?php

/**
 * SlyS
 * 
 * This is a class generated with Zend_CodeGenerator.
 * 
 * @version $Id: Generator.php 985 2011-01-06 08:23:52Z deeper $
 * @license New BSD
 */
class Templater_Model_DbTable_LayoutPoint extends Doctrine_Table
{

    /**
     * Returns an instance of this class.
     * 
     * @return Templater_Model_DbTable_LayoutPoint
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('Templater_Model_Mapper_LayoutPoint');
    }

    /**
     * Remove from DB layout points which not in new points set
     * @param int $layId
     * @param array $newPoints
     * @return boolean
     */
    public function deleteUnusedPoints($layId, array $newPoints)
    {
        $points = $this->createQuery('lp')
                       ->whereNotIn('lp.map_id', $newPoints)
                       ->addWhere('lp.layout_id = ?', array($layId))
                       ->execute();
        return $points->delete();
    }
}

