<?php
/**
 * Doctrine models ZF modules compatibility generator
 *
 * @category   SlyZ
 * @package    SlyZ
 * @copyright  Copyright (c) 2010-2011 Evgheni Poleacov (http://zendmania.com)
 * @license    http://zendmania.com/license/new-bsd New BSD License
 */

// Doctrine doesn't include that file automatically
require_once 'Doctrine/Parser/sfYaml/sfYaml.php';


class Slys_Doctrine_Service_Generator
{
    /**
     *
     * @var array
     */
    protected $_options = array();

    /**
     *
     * @var string
     */
    protected $_tmpDir;

    /**
     *
     * @var Doctrine_Parser_Yml
     */
    protected $_ymlParser;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_tmpDir = dirname(APPLICATION_PATH) . '/tmp/doctrine/';
        $this->_ymlParser = new Doctrine_Parser_Yml();
    }

    /**
     * Set application options
     * @param array $options
     */
    public function setOptions($options)
    {
        $this->_options = $options;
    }

    /**
     * Get applciation options
     * @return array
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * Create database from current connection data
     */
    public function createDatabase()
    {
        try {
            Doctrine_Core::dropDatabases();
        } catch (Exception $ex) {

        }

        Doctrine_Core::createDatabases();
    }

    /**
     * Load data ot database from initial modules fixtures and custom fixtures
     * @param array $config
     * @param array $modules
     * @return string
     */
    public function loadData($config, $modules = array())
    {
        $responce = "Loading fixtures...";
        echo "Loading fixtures...\n";
        $dataFileName = $this->_tmpDir . 'fixtures.yml';

        $fixtures = array();
        foreach ($modules as $name => $dir) {
            $moduleDataYmlPath = realpath($dir . '/..' . $config['data_fixtures_path']);

            if (file_exists($moduleDataYmlPath)) {
                $data = $this->_ymlParser->loadData($moduleDataYmlPath);

                if(!empty($data))
                    foreach($data as $model=>$items) {
                        foreach($items as $key=>$item)
                         $fixtures[$model][$key] = $item;
                    }
            }

        }

        $this->_ymlParser->dumpData($fixtures, $dataFileName);
        $options = $this->getOptions();
        $customFixturesPath = false;

        if(!empty($options['resources']['slys']['config'])) {
            $customFixturesPath = realpath(APPLICATION_PATH.DIRECTORY_SEPARATOR.
                        'configs'.DIRECTORY_SEPARATOR.$options['resources']['slys']['config'].DIRECTORY_SEPARATOR.'fixtures');
        }

        if (file_exists($dataFileName) || $customFixturesPath) {
            Doctrine_Core::loadData($dataFileName);
            Doctrine_Core::loadData($customFixturesPath, true);
            $responce .= "Fixtures imported";
        } else {
            $responce = "Fixtures wasn't found!";
        }
        return $responce;
    }

    /**
     * Generate modules mappers from YAML schema files
     *
     * @param array $modules
     * @param array $config
     * @param boolean $rebuild
     * @return string
     */
    public function generateModelsFromYml($modules, $config, $rebuild = false)
    {
        $response = array();
        $tmpDirectory = $this->_tmpDir;
        Doctrine_Lib::removeDirectories($tmpDirectory);
        $schemas = array();
        foreach ($modules as $name => $dir) {
            if (is_readable(dirname($dir) . '/configs/schema.yml'))
                $schemas[$name] = dirname($dir) . '/configs/schema.yml';
        }

        Doctrine_Core::generateModelsFromYaml(
                        $schemas,
                        $tmpDirectory,
                        $config['generate_models_options']
        );
        $response[] = 'Generated models files.';
        echo "Generating models files...\n";

        if($rebuild) {
            $response[] = $this->createDatabase();

            Doctrine_Core::createTablesFromModels($tmpDirectory);
            $response[] = 'Database tables generated.';
            echo "Database tables generated.\n";
            $response[] = $this->loadData($config, $modules);
        }

        $destinations = $this->createMappersDestinations($modules, $schemas);

        $newFiles = $this->moveMappersToDestinations($tmpDirectory, $destinations);

        $this->removeNotActualMappersFiles($destinations, $newFiles);

        Doctrine_Lib::removeDirectories($tmpDirectory);
        $response[] = 'Done.';
        return $response;
    }

    /**
     * Create default structure of mapper base class
     *
     * @param string $currentClass
     * @param string $parentClassName
     * @param array $methods
     * @return string
     */
    public function _generateSupperMapper($currentClass, $parentClassName, $methods = array())
    {

        $class = new Zend_CodeGenerator_Php_Class();
        $docblock = new Zend_CodeGenerator_Php_Docblock(array(
                    'shortDescription' => "SlyS",
                    'longDescription' => 'This is a class generated with Zend_CodeGenerator.',
                    'tags' => array(
                        array(
                            'name' => 'version',
                            'description' => '$Id: Generator.php 1158 2011-02-02 09:48:32Z deeper $',
                        ),
                        array(
                            'name' => 'license',
                            'description' => 'New BSD',
                        ),
                    ),
                ));
        $class->setName($currentClass)
                ->setExtendedClass($parentClassName)
                ->setDocblock($docblock);


        $class->setMethods($methods);

        $file = new Zend_CodeGenerator_Php_File(array(
                    'classes' => array($class)
                ));

        return $file->generate();
    }

    /**
     * Create table methods
     *
     * @param string $returnClass
     * @param string $tableClass
     * @return array
     */
    public function _getDbTableMethods($returnClass, $tableClass)
    {
        $method = new Zend_CodeGenerator_Php_Method(array(
                    'name' => 'getInstance',
                    'body' => "return Doctrine_Core::getTable('{$returnClass}');",
                    'docblock' => new Zend_CodeGenerator_Php_Docblock(array(
                        'shortDescription' => 'Returns an instance of this class.',
                        'tags' => array(
                            new Zend_CodeGenerator_Php_Docblock_Tag_Return(array(
                                'datatype' => $tableClass,
                            )),
                        ),
                    )),
                ));

        $method->setStatic(true);

        return array($method);
    }

    /**
     * Remove mappers which not found in current shema,
     * but exist on file system from previous generation
     *
     * @param array $destinations
     * @param array $newFiles
     * @return Slys_Doctrine_Service_Generator
     */
    protected function removeNotActualMappersFiles($destinations, $newFiles)
    {
        foreach($destinations as $module=>$dest) {
            $directory = new DirectoryIterator($dest['mappers']);
            foreach($directory as $dir) {
                if($dir->isFile()) {
                    $filename = $dir->getFileInfo()->getBasename('.php');
                    if(strstr($filename, 'Base')) {
                        if(!in_array($filename, $newFiles[$module])) {
                            unlink($dir->getRealPath());
                        }
                    }
                }

            }
        }
        return $this;
    }

    /**
     * Move mappers from temporary directory to modules destinations
     *
     * @param string $tmpDirectory
     * @param array $destinations
     * @return string
     */
    protected function moveMappersToDestinations($tmpDirectory, $destinations)
    {
        $newFiles = array();
        $handle = opendir($tmpDirectory);
        if ($handle) {
            while (false !== ($file = readdir($handle))) {

                if ($file != "." && $file != "..") {
                    $fileParts = explode('_', $file);

                    if (count($fileParts) > 1) {

                        $toModule = strtolower(current($fileParts));

                        if (array_key_exists($toModule, $destinations)) {

                            /**
                             * Move Base Mappers to their real locations
                             */
                            $oldFileName = str_replace('.php', '', $fileParts[count($fileParts) - 1]);
                            $newFileName = 'Base' . $oldFileName;

                            $newFiles[$toModule][] = $newFileName;

                            $source = file_get_contents($tmpDirectory . $file);
                            $destinationFile = $destinations[$toModule]['mappers'] . DIRECTORY_SEPARATOR . $newFileName . '.php';
                            $source = str_replace('_' . $oldFileName, '_' . $newFileName, $source);
                            file_put_contents($destinationFile, $source);
                            unlink($tmpDirectory . $file);

                            /**
                             * Create Super mappers class
                             */
                            unset($fileParts[count($fileParts) - 1]);
                            $destinationFile = $destinations[$toModule]['mappers'] . DIRECTORY_SEPARATOR . $oldFileName . '.php';
                            if (!is_file($destinationFile)) {
                                $basePath = implode('_', $fileParts);
                                $supperClassName = $basePath . '_' . $oldFileName;
                                $supperBaseClassName = $basePath . '_' . $oldFileName;
                                $source = $this->_generateSupperMapper(
                                                $supperClassName, $basePath . '_' . $newFileName
                                );

                                file_put_contents($destinationFile, $source);
                            }
                            /**
                             * Create DbTable class
                             */
                            unset($fileParts[count($fileParts) - 1]);
                            $destinationFile = $destinations[$toModule]['DbTable'] . DIRECTORY_SEPARATOR . $oldFileName . '.php';
                            if (!is_file($destinationFile)) {
                                $basePath = implode('_', $fileParts);
                                $tableClass = $basePath . '_DbTable_' . $oldFileName;
                                $methods = $this->_getDbTableMethods($supperBaseClassName, $tableClass);
                                $source = $this->_generateSupperMapper(
                                                $tableClass, 'Doctrine_Table', $methods
                                );

                                file_put_contents($destinationFile, $source);
                            }
                        }
                    }
                }

            }
            closedir($handle);
        }

        return $newFiles;
    }


    /**
     * Create mappers destinations directories in module structure
     *
     * @param array $modules
     * @return string
     */
    protected function createMappersDestinations($modules, $schemas)
    {
        $directories = array('mappers', 'DbTable');

        $destinations = array();
        $schematicModules = array_keys($schemas);
        foreach ($modules as $moduleName => $modulePath) {
            if(in_array($moduleName, $schematicModules)) {
                foreach ($directories as $directory) {
                    $path = dirname($modulePath) . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . $directory;

                    if(!is_dir($path))
                        Doctrine_Lib::makeDirectories($path);

                    $destinations[$moduleName][$directory] = $path;
                }
            }
        }
        return $destinations;
    }
}