<?php

/**
 * Slys
 *
 * @version    $Id: Users.php 1311 2011-07-25 09:17:59Z zak $
 */
class User_Model_Users extends Slys_Doctrine_Model
{
    public static $uniqueSalt = 121030042012;

    public function getlist()
    {
        return User_Model_DbTable_User::getInstance()->getUsers();
    }

    public function getUser($id)
    {
        return User_Model_DbTable_User::getInstance()->getUser($id);
    }

    public function getUsersPager($page = 1, $maxPerPage = 20, array $filter = array())
    {
        $filter = array_filter($filter);
        return User_Model_DbTable_User::getInstance()->getPager($page, $maxPerPage, $filter);
    }

//
//    /**
//     * Save user to DB
//     *
//     * @param  array $data
//     * @return mixed
//     */
//    public function addUser(array $data)
//    {
//        Zend_Debug::dump($data);
//        $user = new User_Model_Mapper_User;
//        $password = $data['password'];
//        $user->active = true;
//
//        if($this->saveUser($user, $data)) {
//            $user->password = $password;
//            return $user;
//        } else {
//            throw new Zend_Exception('Impossible to create user:'.$user->getErrorStackAsString());
//        }
//    }


    /**
     * Save user entity
     *
     * @param User_Model_Mapper_User $user
     * @param array $data
     * @return boolean
     */
    public function saveUser(User_Model_Mapper_User $user, $data)
    {

        if ($data['password'])
            $data['password'] = sha1(trim($data['password']).self::$uniqueSalt);

        $user->fromArray($data);
        $saveResult = $user->trySave();

        return $saveResult;
    }

}