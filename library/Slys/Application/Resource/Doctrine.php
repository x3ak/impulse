<?php

class Slys_Application_Resource_Doctrine extends Zend_Application_Resource_ResourceAbstract
{

    public $_explicitType = 'doctrine';

    public function __construct($options = null)
    {
        parent::__construct($options);
    }

    public function init()
    {
        set_include_path( implode( PATH_SEPARATOR, array(
                    realpath(ROOT_PATH.'/library/Doctrine'),
                    get_include_path() ) ) );

        $this->getBootstrap()
                ->getApplication()
                ->getAutoloader()
                ->registerNamespace('Doctrine_');

        $this->getBootstrap()
                ->getApplication()
                ->getAutoloader()
                ->registerNamespace('Slys');

        $this->getBootstrap()
                ->getApplication()
                ->getAutoloader()
                ->registerNamespace('ZFDoctrine');
        $config = new Zend_Config($this->getOptions());

        if (empty($config))
            return false;

        $doctrineManager = Doctrine_Manager::getInstance();
        $doctrineManager->registerConnectionDriver('mysql', 'Slys_Doctrine_Connection_Mysql');
        $doctrineManager->setAttribute(Doctrine_Core::ATTR_AUTOLOAD_TABLE_CLASSES, true);

        if(is_object($config->dsn)) {
            foreach ($config->dsn as $key=>$conn) {
                $doctrineConnection = Doctrine_Manager::connection($conn, $key);
            }

            if(empty($config) || !in_array($config->current, array_keys($config->dsn->toArray()))) {
                throw new Zend_Exception('No current connection');
            } else {
                $doctrineManager->setCurrentConnection($config->current);
            }

        } else {
            $doctrineConnection = Doctrine_Manager::connection($config->dsn, 'doctrine');
        }

        $doctrineManager->getCurrentConnection()->setCollate('utf8_unicode_ci');
        $doctrineManager->getCurrentConnection()->setCharset('utf8');
        $doctrineManager->getCurrentConnection()->setAttribute(Doctrine_Core::ATTR_IDXNAME_FORMAT, '%s');

        return $doctrineManager;
    }

}