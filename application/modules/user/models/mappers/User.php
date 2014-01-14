<?php

/**
 * SlyS
 *
 * This is a class generated with Zend_CodeGenerator.
 *
 * @version $Id: User.php 1254 2011-05-04 11:47:12Z deeper $
 * @license New BSD
 */
class User_Model_Mapper_User extends User_Model_Mapper_BaseUser
{
    /**
     * @return bool
     */
    public function isAdmin()
    {
        return $this->role == 'ADMIN';
    }

}

