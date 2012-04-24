<?php

/**
 * SlyS
 * 
 * This is a class generated with Zend_CodeGenerator.
 * 
 * @version $Id: Generator.php 761 2010-12-14 11:49:54Z deeper $
 * @license New BSD
 */
class User_Model_DbTable_Rule extends Doctrine_Table
{

    /**
     * Returns an instance of this class.
     * 
     * @return User_Model_DbTable_Rule
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('User_Model_Mapper_Rule');
    }

    /**
     * Get rules by role and resources
     * @param string $role
     * @param array $resources
     * @return Doctrine_Collection
     */
    public function getRulesByRoleAndResources($role, array $resources)
    {
        return $this->createQuery('rule ru')
                ->leftJoin('ru.Role ro')
                ->addWhere('ro.name = ?', $role)
                ->whereIn('ru.resource_id', $resources)
                ->execute();
    }

    /**
     * Remove from DB rules which resources not in new resources set
     * @param int $itemId
     * @param array $newItems
     * @return boolean
     */
    public function deleteUnusedRules($itemId, array $newItems)
    {
        $newItems[] = 'dummy';
        $items = $this->createQuery('items')
                       ->whereNotIn('items.resource_id', $newItems)
                       ->addWhere('items.role_id = ?', array($itemId))
                       ->execute();
        return $items->delete();
    }
}

