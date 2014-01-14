<?php

/**
 * SlyS
 *
 * This is a class generated with Zend_CodeGenerator.
 *
 * @version $Id: User.php 1279 2011-07-19 09:27:09Z zak $
 * @license New BSD
 */
class User_Model_DbTable_User extends Doctrine_Table
{

    /**
     * Returns an instance of this class.
     *
     * @return User_Model_DbTable_User
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('User_Model_Mapper_User');
    }

    /**
     * Return user entity with role
     * @param int $id
     * @return  User_Model_Mapper_User
     */
    public function getUser($id)
    {
            return Doctrine_Query::create()
                    ->select()
                    ->from('User_Model_Mapper_User user')
                    ->addWhere('user.id = ?', array($id))
                    ->fetchOne();
    }

    public function getUsers()
    {
            $query = Doctrine_Query::create()
                            ->select()
                            ->from('User_Model_Mapper_User user');

            return $query->execute();
    }

    public function getPager($page = 1, $maxPerPage = 20)
    {
        $query = Doctrine_Query::create()
                        ->select()
                        ->from('User_Model_Mapper_User user');


        return new Doctrine_Pager($query, $page, $maxPerPage);
    }
}

