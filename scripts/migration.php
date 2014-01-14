<?php

/**
 * Description
 *
 * @author Serghei Ilin <criolit@gmail.com>
 * @version $Id: migration.php 552 2011-07-01 14:00:06Z criolit $
 */
defined('ROOT_PATH') or define('ROOT_PATH', dirname(dirname(__FILE__)));
defined('APPLICATION_PATH') or define('APPLICATION_PATH', ROOT_PATH.'/application');
defined('APPLICATION_ENV') or define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development'));

set_include_path(
        implode(
                PATH_SEPARATOR, array(
            realpath(ROOT_PATH.'/library'),
            get_include_path(),
                )
        )
);

require_once 'Zend/Application.php';

$application = new Zend_Application(
                APPLICATION_ENV,
                APPLICATION_PATH.'/configs/application.ini'
);

Zend_Controller_Front::getInstance()->setParam(
    'bootstrap',
    $application->bootstrap()->getBootstrap()
);

