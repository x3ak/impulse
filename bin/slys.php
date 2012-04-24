#!/bin/php
<?php
error_reporting(E_ALL);
$options = array('bootstrap' => array('frontcontroller', 'slys', 'doctrine', 'modules'));
$scriptPath = dirname(__FILE__);
require_once $scriptPath.DIRECTORY_SEPARATOR.'config.php';

$formatter = new Doctrine_Cli_AnsiColorFormatter();
/** @var Zend_Application  $application */
$doctrine = $application->getBootstrap()->getResource('doctrine');

echo $formatter->format("\n SlyS Command Line Interface v1.1 \n", array('fg' => 'yellow'));

     $application->getBootstrap()
                ->getApplication()
                ->getAutoloader()
                ->registerNamespace('TempBase_');

$config = array(
    'generate_models_options' => array(
        'pearStyle' => false,
        'generateBaseClasses' => false,
        'tableClassFormat' => "DbTable_%s",
        'baseClassesDirectory' => 'mappers'
    ),
    'models_path' => "/models",
    'sql_path' => "/configs/sql",
    'yaml_schema_path' => "/configs/schema.yml",
    'migrations_path' => "/configs/migrations.yml",
    'data_fixtures_path' => "/configs/data.yml"
);

foreach (Zend_Controller_Front::getInstance()->getControllerDirectory() as $name => $dir) {
    $mapperPath = realpath($dir.'/../models/mappers');
    if ($mapperPath)
        $modelsdirs[$name] = $mapperPath;
}

$schemas = array();
foreach (Zend_Controller_Front::getInstance()->getControllerDirectory() as $name => $dir) {
    $schema = realpath($dir.'/../configs/schema.yml');
    if ($schema)
        $schemas[$name] = $schema;
}

//if (APPLICATION_ENV != 'development')
//    $migrationsPath = sys_get_temp_dir().DIRECTORY_SEPARATOR.'tmp_doctrine_migrations'.time();
//else
    $migrationsPath = APPLICATION_PATH.'/../tmp/migrations';

$generator = new Slys_Doctrine_Service_Generator();
$generator->setOptions($application->getOptions());

if (empty($_SERVER['argv'][1])) {
    $command = '';
} else {
    $command = $_SERVER['argv'][1];
}

switch ($command) {

    case 'doctrine:rebuild': {

            echo $formatter->format("\nDatabase connected...\n");

            $response = $generator->generateModelsFromYml(
                            Zend_Controller_Front::getInstance()->getControllerDirectory(),
                            $config,
                            true);
            if (is_dir($migrationsPath)) {
                Doctrine_Lib::removeDirectories($migrationsPath);
            }
            echo $formatter->format("Done.\n\n", array('fg' => 'green'));
        }
        break;

    case 'doctrine:generate-models': {
            echo $formatter->format("Models generating...\n", array('fg' => 'yellow'));
            $response = $generator->generateModelsFromYml(
                            Zend_Controller_Front::getInstance()->getControllerDirectory(),
                            $config);
            echo $formatter->format("Done.\n", array('fg' => 'green'));
        }
        break;

    case 'doctrine:migrate': {
        echo $formatter->format("\n Migration system under repair and not accessible.\n\n",
                        array('fg' => 'red'));
    } break;
    case 'doctrine:migrate-test': {

            $response = $generator->generateModelsFromYml(
                        Zend_Controller_Front::getInstance()->getControllerDirectory(),
                        $config);

            $dbSchemaPath = APPLICATION_PATH.'/../tmp/db_schema.yml';
            $currentSchemaFile = APPLICATION_PATH.'/../tmp/new_schema.yml';
            if (!is_dir($migrationsPath))
                Doctrine_Lib::makeDirectories($migrationsPath);
            $migration = new Doctrine_Migration($migrationsPath, $doctrine->getCurrentConnection());


            $builder = new Slys_Doctrine_Service_Schema_Builder($dbSchemaPath, $currentSchemaFile, $schemas, $doctrine, $migration);
            $builder->build();

            $migrationDiff = new Doctrine_Migration_Diff($dbSchemaPath, $currentSchemaFile, $migration);
            $changes = $migrationDiff->generateMigrationClasses();
            $prevVersion = $migration->getCurrentVersion();
            if ($prevVersion != $migration->getLatestVersion()) {
                try {
                    $migration->migrate();
                    $result = true;
                } catch (Exception $e) {
                    $result = false;
                }
            } else {
                echo $formatter->format("\n Database already at last version # ".$migration->getCurrentVersion()."\n\n",
                        array('fg' => 'yellow'));
                return false;
            }

            if (!$result) {
                echo $formatter->format("\n {$migration->getNumErrors()} error(s) encountered during migration\n",
                        array('fg' => 'red'));
                $errors = array();
                foreach ($migration->getErrors() as $error) {
                    $errors[] = ' - '.$error->getMessage();
                }
                echo "Next errors found:\n".implode("\n\n", $errors)."\n\n";
            }

            echo $formatter->format("Migrated from version # {$prevVersion} ".
                    "to version # {$migration->getCurrentVersion()}\n\n", array('fg' => 'green'));
            $migration->setCurrentVersion($migration->getLatestVersion());
        }
        break;

    case 'doctrine:cleanup-migrations': {
            if (is_dir($migrationsPath)) {
                Doctrine_Lib::removeDirectories($migrationsPath);
            }
            echo $formatter->format("Previous migrations version removed.\n", array('fg' => 'green'));
        }
        break;

    default:
        echo $formatter->format("\n Not Recognized Command\n", array('fg' => 'white', 'bg' => 'red'));
        echo $formatter->format("\n Available commands:\n", array('fg' => 'green'));
        echo $formatter->format("\t doctrine:rebuild \t\t\t - rebuild database from default schema\n".
//                "\t doctrine:migrate \t\t\t - execute migration from current migrations diff\n".
                "\t doctrine:generate-models \t\t - regenerate models from schema files\n"
//               . "\t doctrine:cleanup-migrations \t\t - remove all migration rollbacks\n \n"
                , array('fg' => 'green'));
}


