<?php

class Slys_Acl extends Zend_Acl
{
    protected static $cache = array();
    protected static $cachePath = '';
    /**
     * Caller context
     * @var Slys_Acl_Caller
     */
    protected $callerContext;

    /**
     * Default acl role
     * @var string
     */
    protected $defaultRole;

    function __construct()
    {
        $applicationPath = str_replace('\\','/', realpath(APPLICATION_PATH));
        self::$cachePath = $applicationPath.'/../data/cache/acl.allowed';

        if(file_exists(self::$cachePath)) {
            self::$cache = include self::$cachePath;
        }
    }


    /**
     * Check if allowed current request
     * @param string $action
     * @param string $controller
     * @param string $module
     * @param string $params
     * @return boolean
     */
    public function isAllowed($role = null, $resource = null, $privilege = null)
    {


        if(empty($role))
            $role = $this->defaultRole;

//        if(!empty(self::$cache[$role.'/'.$resource.'/'.$privilege])) {
//            return self::$cache[$role.'/'.$resource.'/'.$privilege];
//        }





        if($this->has($resource))
            $allow = parent::isAllowed($role, $resource, $privilege);

        if(!$this->has($resource) || ($allow === false && $this->has($resource))) {

            $apiRequest = new Slys_Api_Request($this->callerContext,
                                                'sysmap.get-item-parents-by-identifier',
                                                array('identifier'=>$resource));

            $sysmap = $apiRequest->proceed()->getResponse()->getFirst();

            if(empty($sysmap)) {
//                self::$cache[$role.'/'.$resource.'/'.$privilege] = false;
//                file_put_contents(self::$cachePath,'<?php return '.var_export(self::$cache,true).';' );

                return false;
            }

            foreach($sysmap as $parentResource) {

                $parentItem = $parentResource->getMapIdentifier();

                if(empty($parentItem))
                    continue;

                if($parentResource == $parentItem) {
//                    self::$cache[$role.'/'.$resource.'/'.$privilege] = false;
//                    file_put_contents(self::$cachePath,'<?php return '.var_export(self::$cache,true).';' );

                    return false;
                }

                if(!$this->has($parentItem)) {
                    $this->addResource($parentItem);
                    $this->callerContext->setRules($role, array($parentItem));
                }

                $allow = parent::isAllowed($role, $parentItem, $privilege);

                if($allow) {
//                    self::$cache[$role.'/'.$resource.'/'.$privilege] = true;
//                    file_put_contents(self::$cachePath,'<?php return '.var_export(self::$cache,true).';' );

                    return true;
                }
            }

//            self::$cache[$role.'/'.$resource.'/'.$privilege] = false;
//            file_put_contents(self::$cachePath,'<?php return '.var_export(self::$cache,true).';' );
           return false;
        }

//        self::$cache[$role.'/'.$resource.'/'.$privilege] = $allow;
//        file_put_contents(self::$cachePath,'<?php return '.var_export(self::$cache,true).';' );

        return $allow;
    }

    /**
     * Set default role
     * @param string $role
     * @return Slys_Acl
     */
    public function setDefaultRole($role)
    {
        $this->defaultRole = $role;
        return $this;
    }

    /**
     * Set caller context
     * @param object $context
     * @return Slys_Acl
     */
    public function setCallerContext(Slys_Acl_Caller $context)
    {
        $this->callerContext = $context;
        return $this;
    }

}
