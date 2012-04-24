<?php
defined('ROOT_PATH') or define('ROOT_PATH', dirname( dirname(__FILE__) ) );
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

$application->bootstrap(array('modules','doctrine2'));

$em = $application->getBootstrap()->getResource('doctrine2')->getEntityManager();

$helperSet = new \Symfony\Component\Console\Helper\HelperSet(array(
    'em' => new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper($em)
));

$GLOBALS['doctrine_helperset'] = $helperSet;