<?php

/**
 * Slys
 *
 * @version    $Id: Users.php 1311 2011-07-25 09:17:59Z zak $
 */
class User_Model_Users extends Slys_Doctrine_Model
{

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

    /**
     * Save profile fields
     * @param User_Model_Mapper_User $user
     * @param array $data
     * @return boolean
     */
    public function saveProfile(User_Model_Mapper_User $user, $data)
    {
        $ignoredFields = array('role_id', 'login', 'active','password');

        foreach ($data as $key=>$value) {
            if(in_array($key, $ignoredFields))
                unset($data[$key]);
        }
        $user->fromArray($data);
        return $user->trySave();
    }

    /**
     * Save user password with confirmation with old password
     * @param int $userId
     * @param string $newPassword
     * @param string $oldPassword
     * @return boolean
     */
    public function savePassword(User_Model_Mapper_User $user, $newPassword, $oldPassword = null)
    {
        if($oldPassword !== null && $user->password != md5($oldPassword)) {
            return false;
        }

        $user->password = md5($newPassword);
        $user->save();
        return true;
    }

    /**
     * Save user to DB
     *
     * @param  array $data
     * @return mixed
     */
    public function addUser(array $data)
    {
        Zend_Debug::dump($data);
        $user = new User_Model_Mapper_User;
        $password = $data['password'];
        $user->active = true;

        if($this->saveUser($user, $data)) {
            $user->password = $password;
            return $user;
        } else {
            throw new Zend_Exception('Impossible to create user:'.$user->getErrorStackAsString());
        }
    }

    /**
     * Return user mapper by email
     * @param string $email
     * @return boolean|User_Model_Mapper_User
     */
    public function getUserByEmail($email)
    {
        return User_Model_DbTable_User::getInstance()->findOneByEmail($email);
    }

    /**
     * Return user mapper by token
     * @param string $email
     * @return boolean|User_Model_Mapper_User
     */
    public function getUserByToken($token)
    {
        if(empty($token))
            return false;

        return User_Model_DbTable_User::getInstance()->findOneByToken($token);
    }

    /**
     * Set user token
     * @param User_Model_Mapper_User $user
     * @return boolean
     */
    public function setPasswordToken(User_Model_Mapper_User $user)
    {
        $token = md5(time().$user->email);
        $user->token = $token;
        return $user->trySave();

    }

    /**
     * Reset user token
     * @param User_Model_Mapper_User $user
     * @return boolean
     */
    public function resetPasswordToken(User_Model_Mapper_User $user)
    {
        $user->token = null;
        return $user->trySave();

    }

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
            $data['password'] = md5($data['password']);

        $user->fromArray($data);
        $saveResult = $user->trySave();


        return $saveResult;
    }

}