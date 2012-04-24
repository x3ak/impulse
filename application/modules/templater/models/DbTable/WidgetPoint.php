<?php

/**
 * SlyS
 * 
 * This is a class generated with Zend_CodeGenerator.
 * 
 * @version $Id: Generator.php 985 2011-01-06 08:23:52Z deeper $
 * @license New BSD
 */
class Templater_Model_DbTable_WidgetPoint extends Doctrine_Table
{

    /**
     * Returns an instance of this class.
     * 
     * @return Templater_Model_DbTable_WidgetPoint
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('Templater_Model_Mapper_WidgetPoint');
    }

    /**
     * Remove from DB layout points which not in new points set
     * @param int $layId
     * @param array $newPoints
     * @return boolean
     */
    public function deleteUnusedPoints($wdId, array $newPoints)
    {
        $points = $this->createQuery('point')
                       ->whereNotIn('point.map_id', $newPoints)
                       ->addWhere('point.widget_id = ?', array($wdId))
                       ->execute();
        return $points->delete();
    }
}

