<?php

class User_Library_Acl extends Zend_Acl
{

    /**
     * Current copy config
     * @var array
     */
    protected $_config;

    protected $_initilized = false;

    /**
     * Init Slys_Acl object from acl.ini file
     */
    public function __construct(Zend_Config $aclConfig = null)
    {
        $this->_config = $aclConfig;
        if(!empty($this->_config)) {
            $this->_addRoles($this->_config->roles);
            $this->_addResources($this->_config->resources);
            $this->_initilized = true;
        }
    }

    /**
     * Check if ACL was initialized
     * @return boolean
     */
    public function isInitialized()
    {
        return $this->_initilized;
    }

    /**
     * Set init semaphore
     * @param boolean $flag
     * @return User_Library_Acl
     */
    public function setInitialized($flag)
    {
        $this->_initilized = $flag;
        return $this;
    }

    /**
     * Return current config
     * @return Zend_Config
     */
    public function getConfig()
    {
        return $this->_config;
    }

    /**
     * Set ACL config
     * @param array $config
     */
    public function setConfig($config)
    {
        $this->_config = $config;
        return $this;
    }

    /**
     * Adding roles list to ACL object
     * @param array $roles
     * @return void
     */
    public function _addRoles($roles)
    {
        foreach ($roles as $name => $parents) {
            if (!$this->hasRole($name)) {
                if (empty($parents))
                    $parents = null;
                else
                    $parents = explode(',', $parents);

                $this->addRole(new Zend_Acl_Role($name), $parents);
            }
        }
    }

    /**
     * Adding resources list to ACL object
     * @param array $resources
     * @return void
     */
    public function _addResources($resources)
    {
        // first create all resources
        foreach ($resources as $rule => $roles) {
            foreach ($roles as $role => $resources) {
                foreach ($resources as $resourceName) {

                    $resource = explode('.', $resourceName);

                    // module.controller;
                    // action will be as a privilege
                    $resourceId = $resource[0] . '.' . $resource[1];

                    if (!$this->has($resourceId))
                        $this->addResource(new Zend_Acl_Resource($resourceId));

                    $privelege = (empty($resource[2]) or $resource[2] == '*') ? null : array($resource[2]);
                    $assertion = null;

                    if (!empty($resource[3])) {
                        parse_str($resource[3], $params);
                        $assertion = new Slys_Acl_Assertion_Params($params, $rule);
                        $resourceId .= ':' . str_replace('&', ':', str_replace('=', ':', $resource[3]));
                    }

                    if ($rule == 'allow')
                        $this->allow($role, $resourceId, $privelege, $assertion);
                    else
                        $this->deny($role, $resourceId, $privelege, $assertion);
                }
            }
        }
    }

    /**
     * Override base method to return a resource with deny access to all roles
     * in case if the requested resource doesn't exists
     * @see library/Zend/Zend_Acl::get()
     */
    public function get($resource)
    {
        try {
            $instance = parent::get($resource);
        } catch (Zend_Acl_Exception $exception) {
            $instance = new Zend_Acl_Resource($resource);
            $this->addResource($instance);
            $this->deny(null, $instance);
        }

        return $instance;
    }

    /**
     * Checking real and virtual ACL resources existence
     * 
     * @param string $resource
     */
    public function hasResource($resource, $role = false)
    {
        if ($this->has($resource))
            return true;
        $resourceParts = explode('.', $resource);
        if ($this->has($resourceParts[0] . '.*'))
            return true;

        return false;
    }

    /**
     * Checking access to real and virtual ACL resources and privelege for indicated role
     * 
     * @param string $role
     * @param string $resource
     * @param string $privilege
     * @see library/Zend/Zend_Acl::isAllowed()
     */
    public function isAllowed($role = null, $resource = null, $privilege = null)
    {
        $initial_resource = $resource;
        foreach ($this->getParentRoles($role) as $inheritedRole) {
            $appr = $this->getAppropriateResourceOfRole($initial_resource, $inheritedRole, $privilege);
            if (!empty($appr))
                $resource = $appr;
        }
        return parent::isAllowed($role, $resource, $privilege);
    }

    /**
     * Return appropriate existing resource for called resource name
     * 
     * @param string $current_resource
     * @param string $role
     * @param string $privelege
     */
    public function getAppropriateResourceOfRole($current_resource, $role, $privilege)
    {
        $rules = $this->_rules['byResourceId'];
        $resourceParts = explode('.', $current_resource);

        $extendedRules = array();
        foreach ($rules as $resName => $rule) {
            $mcExtAccess = "{$resourceParts[0]}.{$resourceParts[1]}:";

            if (strstr($resName, $mcExtAccess)) {

                if (!empty($rule['byRoleId'][$role]['byPrivilegeId'])) {
                    if (!empty($rule['byRoleId'][$role]['byPrivilegeId'][$privilege])) {
                        $assert = $rule['byRoleId'][$role]['byPrivilegeId'][$privilege]['assert'];
                        if (!empty($assert) && $assert->assert($this, new Zend_Acl_Role($role), new Zend_Acl_Resource($resName), $privilege) == true)
                            return $resName;
                    }
                }

                if (!empty($rule['byRoleId'][$role]['allPrivileges'])) {
                    $assert = $rule['byRoleId'][$role]['allPrivileges']['assert'];
                    if (!empty($assert) && $assert->assert($this, new Zend_Acl_Role($role), new Zend_Acl_Resource($resName), $privilege) == true)
                        return $resName;
                }
            }

            $mcExtAccess = "{$resourceParts[0]}.*:";
            if (strstr($resName, $mcExtAccess)) {
                if (!empty($rule['byRoleId'][$role]['byPrivilegeId'])) {
                    if (!empty($rule['byRoleId'][$role]['byPrivilegeId'][$privilege])) {
                        $assert = $rule['byRoleId'][$role]['byPrivilegeId'][$privilege]['assert'];
                        if (!empty($assert) && $assert->assert($this, new Zend_Acl_Role($role), new Zend_Acl_Resource($resName), $privilege) == true)
                            return $resName;
                    }
                }
                if (!empty($rule['byRoleId'][$role]['allPrivileges'])) {
                    $assert = $rule['byRoleId'][$role]['allPrivileges']['assert'];
                    if (!empty($assert) && $assert->assert($this, new Zend_Acl_Role($role), new Zend_Acl_Resource($resName), $privilege) == true)
                        return $resName;
                }
            }
        }

        //Checking simple virtual resources
        $mcAccess = "{$resourceParts[0]}.{$resourceParts[1]}";
        if (!empty($rules[$mcAccess]['byRoleId'][$role]['byPrivilegeId'])) {
            if (!empty($rules[$mcAccess]['byRoleId'][$role]['byPrivilegeId'][$privilege])) {
                return $mcAccess;
            }
        }
        if (!empty($rules[$mcAccess]['byRoleId'][$role]['allPrivileges'])) {
            return $mcAccess;
        }

        $mwAccess = "{$resourceParts[0]}.*";
        if (!empty($rules[$mwAccess]['byRoleId'][$role]['byPrivilegeId'])) {
            if (!empty($rules[$mwAccess]['byRoleId'][$role]['byPrivilegeId'][$privilege])) {
                return $mwAccess;
            }
        }

        if (!empty($rules[$mwAccess]['byRoleId'][$role]['allPrivileges'])) {
            return $mwAccess;
        }

        return false;
    }

    /**
     * Return parent roles sorted by age
     * 
     * @param string $role
     * @return array
     */
    public function getParentRoles($role)
    {
        $inheritedRoles = array();
        foreach ($this->getRoles() as $prole) {
            if ($this->inheritsRole($role, $prole))
                $inheritedRoles[] = $prole;
        }
        $inheritedRoles[] = $role;
        return $inheritedRoles;
    }

}