<?php

/**
 * SlyS
 * 
 * This is a class generated with Zend_CodeGenerator.
 * 
 * @version $Id: Theme.php 1224 2011-04-04 13:58:41Z deeper $
 * @license New BSD
 */
class Templater_Model_DbTable_Theme extends Doctrine_Table
{

    /**
     * Returns an instance of this class.
     * 
     * @return Templater_Model_DbTable_Theme
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('Templater_Model_Mapper_Theme');
    }

    /**
     * Themes pager
     * @param int $page
     * @param int $maxPerPage
     * @return Doctrine_Pager
     */
    public function getPager($page = 1, $maxPerPage = 20)
    {
        $query = Doctrine_Query::create()
            ->select('tpl.*, lay.*')
            ->from('Templater_Model_Mapper_Theme tpl')
            ->leftJoin('tpl.Layouts lay');

        return new Doctrine_Pager($query, $page, $maxPerPage);
    }
    
    /**
     * Return current theme
     * @return Templater_Model_Mapper_Theme 
     */
    public function getCurrentTheme()
    {
       return $this->findOneByCurrent(true);
    }

    /**
     * Return themes list with all layouts assigned to it
     * @param int $id - theme ID
     * @return Templater_Model_Mapper_Theme
     */
    public function getThemeWithLayouts($id)
    {
        $query = Doctrine_Query::create()
                ->select('tpl.*, lay.*')
                ->from('Templater_Model_Mapper_Theme tpl')
                ->leftJoin('tpl.Layouts lay')
                ->addWhere('tpl.id = ?', array($id));
        return $query->fetchOne();
    }
}

