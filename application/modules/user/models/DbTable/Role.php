<?php

/**
 * SlyS
 * 
 * This is a class generated with Zend_CodeGenerator.
 * 
 * @version $Id: Role.php 1268 2011-07-19 08:23:32Z deeper $
 * @license New BSD
 */
class User_Model_DbTable_Role extends Doctrine_Table
{

    /**
     * Returns an instance of this class.
     * 
     * @return User_Model_DbTable_Role
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('User_Model_Mapper_Role');
    }

    /**
     * Return role marked as default
     * @return User_Model_Mapper_Role
     */
    public function getDefaultRole()
    {
        return $this->findOneByDefaul(1);
    }

    /**
     * Return one user role by ID with rules
     * @param int $id
     * @return User_Model_Mapper_Role
     */
    public function getRole($id)
    {
        return $this->createQuery('role')
                ->select('role.*, rule.*')
                ->leftJoin('role.Rules rule')
                ->addWhere('role.id = ?', $id)
                ->fetchOne();
    }

    /**
     * Return all roles list
     * @return Doctrine_Collection
     */
    public function getRoles()
    {
        $query = Doctrine_Query::create()
                        ->select('role.*')
                        ->from('User_Model_Mapper_Role role')
                        ->addOrderBy('role.parent_id ASC')
                        ->addOrderBy('role.id ASC');

        return $query->execute();
    }

    /**
     * Return pager of current table
     * @param int $page
     * @param int $maxPerPage
     * @return Doctrine_Pager
     */
    public function getPager($page = 1, $maxPerPage = 20)
    {
        $query = Doctrine_Query::create()
                        ->select('role.*')
                        ->from('User_Model_Mapper_Role role');

        return new Doctrine_Pager($query, $page, $maxPerPage);
    }

}

