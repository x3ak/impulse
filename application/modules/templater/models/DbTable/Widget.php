<?php

/**
 * SlyS
 *
 * This is a class generated with Zend_CodeGenerator.
 *
 * @version $Id: Widget.php 1320 2011-07-28 07:34:59Z zak $
 * @license New BSD
 */
class Templater_Model_DbTable_Widget extends Doctrine_Table
{

    /**
     * Returns an instance of this class.
     *
     * @return Templater_Model_DbTable_Widget
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('Templater_Model_Mapper_Widget');
    }

    public function getWidgets()
    {
        $query = Doctrine_Query::create()
                        ->select('wd.*')
                        ->from('Templater_Model_Mapper_Widget wd')
                        ->leftJoin('wd.Layout lay');

        return $query->execute();
    }

    public function getPager($page = 1, $maxPerPage = 20)
    {
        $query = Doctrine_Query::create()
                        ->select('wd.*, lay.*, tpl.*')
                        ->from('Templater_Model_Mapper_Widget wd')
                        ->leftJoin('wd.Layout lay')
                        ->leftJoin('lay.Theme tpl');

        return new Doctrine_Pager($query, $page, $maxPerPage);
    }

    public function getLayoutWithWidgetsbyNameAndRequest($layoutName, $mapIds = array())
    {
        $ids = array();
        foreach($mapIds as $mapId) {
            if($mapId instanceof Sysmap_Model_Mapper_Sysmap)
                $ids[] = $mapId->hash;
        }

        $query = Doctrine_Query::create()
                        ->select('lay.*, w.*, wp.*, wt.* ')
                        ->from('Templater_Model_Mapper_Layout lay')
                        ->innerJoin('lay.Widgets w')
                        ->innerJoin('w.WidgetPoints wp')
                        ->whereIn('wp.map_id', $ids)
                        ->addOrderBy('w.ordering DESC');

        return $query->fetchOne();
    }

    /**
     * Return widget by id with all widget points
     * @param int $wdId
     * @return Templater_Model_Mapper_Widget
     */
    public function getWidgetWithWidgetPoints($wdId)
    {
        return $this->createQuery('wd')
                    ->leftJoin('wd.WidgetPoints wp')
                    ->addWhere('wd.id = ?', array($wdId))
                    ->fetchOne();
    }
}

