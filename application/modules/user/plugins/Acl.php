<?php

/**
 * Slys
 *
 * Acl plugin for restrict access to resource
 *
 * @author Evgheni Poleacov <evgheni.poleacov@gmail.com>
 */
class User_Plugin_Acl extends Zend_Controller_Plugin_Abstract implements Slys_Acl_Caller
{

    /**
     * Copy of ACL object
     * @var User_Library_Acl
     */
    protected $_acl;
    /**
     * Copy of identity object
     * @var Doctine_Record|array|null
     */
    protected $_identity;

    /**
     * Current role instance
     * @var Zend_Acl_Role
     */
    protected $_currentRole;

    /**
     * Contructor waiting Zend_Acl instance
     * @param Slys_Acl $acl
     */
    public function __construct(Slys_Acl $acl)
    {
        $this->_acl = $acl;
        $acl->setCallerContext($this);
    }

    /**
     * Rerturn current ACL object
     * @return Slys_Acl
     */
    public function getAcl()
    {
        return $this->_acl;
    }

    /**
     * Prepare ACL before route startup and check if current request is allowed
     *
     * @param Zend_Controller_Request_Abstract $request
     */
    public function routeShutdown(Zend_Controller_Request_Abstract $request)
    {
        /**
         * Init ACL it now not in contructor because possible some resources not initilized
         */
        $result = $this->_initAcl();
        if(!$result)
            return false;

        Zend_View_Helper_Navigation_HelperAbstract::setDefaultAcl($this->_acl);

        if(Zend_Auth::getInstance()->hasIdentity()) {
            if (!empty(Zend_Auth::getInstance()->getIdentity()->Role->name))
                $this->_currentRole = new Zend_Acl_Role(Zend_Auth::getInstance()->getIdentity()->Role->name);
        }

         if (empty($this->_currentRole))
                throw new Zend_Exception('Please provide default user role in users module config file');

        $this->_acl->setDefaultRole($this->_currentRole);
        Zend_View_Helper_Navigation_HelperAbstract::setDefaultRole($this->_currentRole);

        $allow = false;
        foreach($this->_acl->getResources() as $resource) {
            if($this->_acl->isAllowed($this->_currentRole, $resource))
                $allow = true;
        }

        if(!$allow) {
            $routeName = Zend_Controller_Front::getInstance()->getRouter()->getCurrentRouteName();
            $front = Zend_Controller_Front::getInstance();
            if ($routeName == 'admin')
                $controller = 'admin';
            else
                $controller = 'auth';

            $request->setActionName('login')
                    ->setControllerName($controller)
                    ->setModuleName('user');
        }
    }


    /**
     * Get ACL information from DB
     * @return User_Plugin_Acl
     */
    protected function _initAcl()
    {
        $rolesRows = User_Model_DbTable_Role::getInstance()->getRoles();

        $roles = array();

        foreach($rolesRows as $roleRow) {
            $roles[$roleRow->id] = $roleRow->toArray();
            if($roleRow->is_default)
                $this->_currentRole = new Zend_Acl_Role($roleRow->name);
        }



        foreach($roles as $role) {
            $parent = null;
            if(!empty($role['parent_id'])) {
                $parent = $roles[$role['parent_id']]['name'];
            }

            $this->_acl->addRole(new Zend_Acl_Role($role['name']), $parent);
        }

        $apiRequest = new Slys_Api_Request($this, 'sysmap.currently-active-items');

        $activeResources = $apiRequest->proceed()->getResponse()->getFirst();

        if(empty($activeResources))
            return false;

        foreach($activeResources as $resource) {
            if($resource instanceof Sysmap_Model_Mapper_Sysmap) {
                $mapId = $resource->getMapIdentifier();
                if(!empty($mapId))
                    $this->_acl->addResource($mapId);
            }
        }

        $activeResources = $this->_acl->getResources();
        if(empty($activeResources))
            return false;

        foreach($this->_acl->getRoles() as $role) {
            $this->setRules($role, $this->_acl->getResources());
        }

        return $this;
    }

    /**
     * Set ACL rule from DB
     * @param string $role
     * @param string $resourceId
     * @return User_Plugin_Acl
     */
    public function setRules($role, $resources)
    {
        $rules = User_Model_DbTable_Rule::getInstance()
            ->getRulesByRoleAndResources($role, $resources);
        if($rules->count() > 0) {
            foreach($rules as $rule) {
                $this->_acl->{$rule->rule}($role, $rule->resource_id);
            }

        }
        return $this;
    }

}