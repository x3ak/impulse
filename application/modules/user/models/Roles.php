<?php

/**
 * Slys
 *
 * @version    $Id: Roles.php 1268 2011-07-19 08:23:32Z deeper $
 */
class User_Model_Roles
{

    /**
     * Return Roles list
     * @return array
     */
    public function getlist()
    {
        return User_Model_DbTable_Role::getInstance()->getRoles();
    }

    /**
     * Return user role by Id of empty if not found and request update
     * @param int $id
     * @param boolean $forUpdate
     * @return User_Model_Mapper_User
     */
    public function getRole($id, $forUpdate = false)
    {
        $role = User_Model_DbTable_Role::getInstance()->getRole($id);
        if ($forUpdate && empty($role))
            $role = new User_Model_Mapper_User();
        return $role;
    }

    /**
     * Roles pager
     * @param int $page
     * @param int $maxPerPage
     * @return Doctrine_Pager
     */
    public function getRolesPager($page = 1, $maxPerPage = 20)
    {
        return User_Model_DbTable_Role::getInstance()->getPager($page, $maxPerPage);
    }

    /**
     *
     * @param User_Model_Mapper_Role $role
     * @param array $values
     * @return boolean
     */
    public function saveRole(User_Model_Mapper_Role $role, $values)
    {
        $values['resources'] = (array)$values['resources'];
        $role->fromArray($values);
        foreach($role->Rules as $rule) {
            if(!in_array($rule->resource_id, $values['resources'])) {
                $rule->delete();
            }
        }

        $role->clearRelated('Rules');

        if(!empty($values['resources'])) {
            foreach($values['resources'] as $key=>$mapId) {
                $rule = User_Model_DbTable_Rule::getInstance()->findByDql("resource_id = ? AND role_id = ?", array($mapId, $role->id));

                if($rule->count() < 1) {
                    $rule = new User_Model_Mapper_Rule();
                    $rule->set('resource_id', $mapId);
                    $rule->set('role_id', $role->id);
                    $rule->set('rule', 'allow');
                    $role->Rules->add($rule);
                }
            }
        }

        return $role->save();
    }


    /**
     * Return roles which can be used for public registration
     * 
     * @return Doctrine_Collection
     */
    public function getRegistrationRoles()
    {
        $roles = User_Model_DbTable_Role::getInstance()->findByRegister(true);
        return $roles;
    }
}