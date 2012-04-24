<?php

/**
 * SlyS
 *
 * This is a class generated with Zend_CodeGenerator.
 *
 * @version $Id: Generator.php 761 2010-12-14 11:49:54Z deeper $
 * @license New BSD
 */
class Sysmap_Model_DbTable_Sysmap extends Doctrine_Table
{

    /**
     * Returns an instance of this class.
     *
     * @return Sysmap_Model_DbTable_Sysmap
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('Sysmap_Model_Mapper_Sysmap');
    }

    /**
     * Returns root element from map.
     * @return Sysmap_Model_Mapper_Sysmap
     */
    public function getRootElement()
    {
        return Doctrine_Query::create()
            ->select()
            ->from('Sysmap_Model_Mapper_Sysmap')
            ->where('level = 0')
            ->fetchOne();
    }

    /**
     * Finds modules in in map.
     *
     * @param array $params              query parameters (a la PDO)
     * @param int $hydrationMode         Doctrine_Core::HYDRATE_ARRAY or Doctrine_Core::HYDRATE_RECORD
     * @return Doctrine_Collection|array Depends from $hydrationMode can be collection of Sysmap_Model_Mapper_Sysmap
     */
    public function findModules($params = array(), $hydrationMode = null)
    {
        return Doctrine_Query::create()
            ->select()
            ->from('Sysmap_Model_Mapper_Sysmap')
            ->where('level < 2')
            ->execute($params,$hydrationMode);
    }

    /**
     * Finds controllers in in map.
     *
     * @param array $params              query parameters (a la PDO)
     * @param int $hydrationMode         Doctrine_Core::HYDRATE_ARRAY or Doctrine_Core::HYDRATE_RECORD
     * @return Doctrine_Collection|array Depends from $hydrationMode can be collection of Sysmap_Model_Mapper_Sysmap
     */
    public function findControllers($params = array(), $hydrationMode = null)
    {
        return Doctrine_Query::create()
            ->select()
            ->from('Sysmap_Model_Mapper_Sysmap')
            ->where('level = 2')
            ->execute($params,$hydrationMode);
    }

    /**
     * Gets the list of all actions for specified module-controller
     * @param  $moduleName
     * @param  $controllerName
     * @param  array $params
     * @param  null $hydrationMode
     * @return Doctrine_Collection
     */
    public function findActions($moduleName, $controllerName, $params = array(), $hydrationMode = null)
    {
        return Doctrine_Query::create()
            ->select()
            ->from('Sysmap_Model_Mapper_Sysmap')
            ->where('mca like ?', $moduleName.'.'.$controllerName.'.%')
            ->andWhere('level = 3')
            ->execute($params,$hydrationMode);
    }

    /**
     * Allows to find mapper by module controller action names
     * @param string $moduleName
     * @param string $controllerName
     * @param string $actionName
     * @return Sysmap_Model_Mapper_Sysmap
     */
    public function findAction($moduleName, $controllerName, $actionName)
    {
        $mcaToFind = Sysmap_Model_Map::getInstance()->formatMcaName(array('module'=>$moduleName, 'controller' => $controllerName, 'action' => $actionName));

        return $this->createQuery()
                ->select()
                ->where('mca = ?', $mcaToFind)
                ->andWhere('level = 3')
                ->limit(1)
                ->execute()->getFirst();

    }

    /**
     * Clear records with the passed id(s)
     * @param  string|array $ids
     * @return void
     */
    public function deleteRecords($ids)
    {
        if (is_string($ids))
            $ids = array(array('id' => $ids));

        foreach($ids as $id)
            $this->findOneBy('id', $id['id'])->getNode()->delete();
    }

    /**
     * Returns all non empty
     * @return Doctrine_Collection
     */
    public function getNonEmtyMca()
    {
        return Doctrine_Query::create()->select('mca')
                                       ->from('Sysmap_Model_Mapper_Sysmap')
                                       ->where('mca is not null')
                                       ->execute();
    }
}

