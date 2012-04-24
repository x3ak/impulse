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


echo "Reindex MCA...";
$reindexStart = microtime(1);

$sysmapRoot = Sysmap_Model_DbTable_Sysmap::getInstance()->find(1);
$sysmapRoot->index_date = '1970-01-01 00:00:00';
$sysmapRoot->save();

Sysmap_Model_Map::getInstance()->reindexMCA();

printf(" finished in %0.6f sec. \r\n",(microtime(1)-$reindexStart));

/**
 * AFTER THIS COMMENT GOES YOUR CODE! PLEASE WRITE THE AUTHOR OF THE CODE!
 */


$navDisplayMeny = Sysmap_Model_DbTable_Sysmap::getInstance()->findAction('navigation','index','display-menu');
Sysmap_Model_Map::getInstance()->addExtend(array(
    'sysmap_id' => $navDisplayMeny->id,
    'title' => 'Admin navigation provider',
    'params' => unserialize('a:3:{s:7:"item_id";a:1:{i:0;s:1:"9";}s:3:"css";s:0:"";s:7:"partial";s:0:"";}')
));

Sysmap_Model_Map::getInstance()->addExtend(array(
    'sysmap_id' => $navDisplayMeny->id,
    'title' => 'Developer navigation provider',
    'params' => unserialize('a:3:{s:7:"item_id";a:1:{i:0;s:1:"2";}s:3:"css";s:0:"";s:7:"partial";s:0:"";}')
));

$SQL = <<<SQL
INSERT INTO templater_layout_points VALUES (NULL, '1-e435cb5103d8de8c76bd58a7f3523188',2);
INSERT INTO templater_layout_points VALUES (NULL, '1-c10e1b0bf20bb62a278787ceb4ac35ea',2);
INSERT INTO templater_widget_points VALUES (NULL, '1-e435cb5103d8de8c76bd58a7f3523188',1);
INSERT INTO templater_widget_points VALUES (NULL, '1-e435cb5103d8de8c76bd58a7f3523188',2);
INSERT INTO templater_widget_points VALUES (NULL, '1-e435cb5103d8de8c76bd58a7f3523188',3);
SQL;

Doctrine_Manager::getInstance()->getCurrentConnection()->exec($SQL);
/**
 * END OF THE MIGRATION PHP CODE
 */
Navigation_Model_Navigation::getInstance()->clearCache();