<?php
/**
 * Slys index file
 *
 * @author Serghei Ilin <criolit@gmail.com>
 */

defined('ROOT_PATH') or define('ROOT_PATH', dirname( dirname(__FILE__) ) ) ;
defined('APPLICATION_PATH') or define('APPLICATION_PATH', ROOT_PATH . '/application');
defined('APPLICATION_ENV') or define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development'));

set_include_path(
    implode(
        PATH_SEPARATOR, array(
            realpath(ROOT_PATH . '/library'),
            get_include_path(),
        )
    )
);

require_once 'Zend/Application.php';

$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);
if(!empty($options['bootstrap']))
	$bootstrap = $options['bootstrap'];
else
	$bootstrap = null;

$application->bootstrap($bootstrap);