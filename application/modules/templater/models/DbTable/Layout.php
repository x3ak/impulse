<?php

/**
 * SlyS
 *
 * This is a class generated with Zend_CodeGenerator.
 *
 * @version $Id: Layout.php 1097 2011-01-24 08:38:38Z criolit $
 * @license New BSD
 */
class Templater_Model_DbTable_Layout extends Doctrine_Table
{

    /**
     * Returns an instance of this class.
     *
     * @return Templater_Model_Mapper_Layout
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('Templater_Model_Mapper_Layout');
    }

    public function getLayoutWithWidgetsByName($name)
    {
        return Doctrine_Query::create()
                ->select('lay.*, wd.*')
                ->from('Templater_Model_Mapper_Layout lay')
                ->leftJoin('lay.Widgets wd')
                ->addWhere('lay.name = ?', array($name))
                ->addOrderBy('wd.ordering')
                ->fetchOne();
    }

    /**
     * Return layout attached to current map indentifiers
     * @param Doctrine_Collection $identifiers
     * @return Templater_Model_Mapper_Layout
     */
    public function getCurrentLayout($identifiers)
    {
        $query = Doctrine_Query::create()
            ->select('lay.*, tpl.*')
            ->from('Templater_Model_Mapper_Layout lay')
            ->leftJoin('lay.Theme tpl')
            ->leftJoin('lay.Points lp')
            ->addWhere('lay.published = ?', array(true))
            ->addWhere('tpl.current = ?', array(true))
            ->addOrderBy('lp.map_id DESC');

        $layoutParts = array();
        foreach($identifiers as $identifier) {
            $layoutParts[] = $identifier->getMapIdentifier();
        }

        $query->whereIn('lp.map_id', $layoutParts);

        $layout = $query->fetchOne();

        if(empty($layout))
            return $this->getDefaultLayout();
        return $layout;
    }

    /**
     * Return default layout for current theme by default options
     * @return Templater_Model_Mapper_Layout
     */
    public function getDefaultLayout()
    {
        $defaults = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOption('templater');
        $routeName = Zend_Controller_Front::getInstance()->getRouter()->getCurrentRouteName();
        $defLayName = 'default';
        if($routeName == 'admin')
            $defLayName = $routeName;

        $defaultLayoutName = $defaults['layout'][$defLayName];

        $query = Doctrine_Query::create()
            ->select('lay.*, tpl.*')
            ->from('Templater_Model_Mapper_Layout lay')
            ->leftJoin('lay.Theme tpl')
            ->addWhere('lay.published = :published', array('published'=>true))
            ->addWhere('tpl.current = :current', array('current'=>true))
            ->addWhere('lay.name = :lay_name', array('lay_name'=>$defaultLayoutName));
        return $query->fetchOne();
    }

    /**
     * Return pager instance of layout table
     *
     * @param int $page
     * @param int $maxPerPage
     * @param array $where
     * @return Doctrine_Pager
     */
    public function getPager($page = 1, $maxPerPage = 20, $where = array())
    {
        $query = Doctrine_Query::create()
                        ->select('lay.*, tpl.*')
                        ->from('Templater_Model_Mapper_Layout lay')
                        ->leftJoin('lay.Theme tpl');

        if (!empty($where) && is_array($where))
            foreach ($where as $field => $value) {
                $query->addWhere("lay.{$field} = ? ", array($value));
            }
        return new Doctrine_Pager($query, $page, $maxPerPage);
    }

    /**
     * Return layout by id with all layout points
     * @param int $layId
     * @return Templater_Model_Mapper_Layout
     */
    public function getLayoutWithLayoutPoints($layId)
    {
        return $this->createQuery('lay')
                    ->leftJoin('lay.Points lp')
                    ->addWhere('lay.id = ?', array($layId))
                    ->fetchOne();
    }

}

