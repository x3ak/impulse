<?php
/**
 * Doctrine database schemas builder 
 *
 * @category   SlyZ
 * @package    SlyZ
 * @copyright  Copyright (c) 2010-2011 Evgheni Poleacov (http://zendmania.com)
 * @license    http://zendmania.com/license/new-bsd New BSD License
 */

class Slys_Doctrine_Service_Schema_Builder
{

    protected $_dbFileName;
    protected $_curFileName;
    /**
     *
     * @var Doctrine_Manager;
     */
    protected $_connection;
    protected $_newSchemas;
    protected $_ymlParser;
    protected $_correspondence = array();
    protected $_slysMigPrefix = 'slys_migration_';

    /**
     * Prepating builder for work
     *
     * @param string $dbSchemaFilePath
     * @param string $currentSchemaFilePath
     * @param array $newSchemas
     * @param Doctrine_Manager $connection
     * @param Doctrine_Migration $migration
     */
    public function __construct($dbSchemaFilePath, $currentSchemaFilePath, $newSchemas, $connection, $migration)
    {
        $this->_dbFileName = $dbSchemaFilePath;
        $this->_curFileName = $currentSchemaFilePath;
        $this->_connection = $connection;
        $this->_newSchemas = $newSchemas;
        $this->_migration = $migration;

        $this->formatter = new Doctrine_Cli_AnsiColorFormatter();
        $this->_ymlParser = new Doctrine_Parser_Yml();
        $this->_filter = new Zend_Filter_Word_SeparatorToCamelCase('_');;
    }

    /**
     * Build scheams from DB and current schema to files
     */
    public function build()
    {
        $dbSchemas = $this->removeUnusedElements($this->loadDbSchemas());
        if(empty($dbSchemas))
            $dbSchemas = $this->createEmptyDbSchema();

        $this->_ymlParser->dumpData($dbSchemas, $this->_dbFileName);
        
        $newSchemas = $this->removeUnusedElements($this->loadNewSchemas());
        $this->_ymlParser->dumpData($newSchemas, $this->_curFileName);
    }

    /**
     * Return schema from current version in compatibility mode
     * @return array
     */
    protected function loadNewSchemas()
    {

        $fileName = $this->createMigrationTables($this->_newSchemas);

        $schema = $this->_ymlParser->loadData($this->_curFileName);

        foreach($schema as $key=>$value) {
            if(!strstr($value['tableName'], $this->_slysMigPrefix))
                    unset($schema[$key]);
        }
        $this->_ymlParser->dumpData($schema, $this->_curFileName);

        $classPrefix = str_replace('_','',$this->_filter->filter($this->_slysMigPrefix));
        $file = file_get_contents($this->_curFileName);
        $file = str_replace($this->_slysMigPrefix, '', $file);
        $file = str_replace($classPrefix, '', $file);
        file_put_contents($this->_curFileName, $file);

        $dbImporter = new Doctrine_Import_Schema();
        $schema = $dbImporter->buildSchema($this->_curFileName, 'yml');
        $schema = $this->ksortRecursive($schema);
        return $schema;
        
    }

    /**
     * Return schema from DB with in compatibility mode
     * @return array
     */
    protected function loadDbSchemas()
    {
        try {
            Doctrine_Core::generateYamlFromDb(
                            $this->_dbFileName,
                            array('doctrine')
            );
        } catch (Doctrine_Exception $e) {
            echo $this->formatter->format("\n{$e->getMessage()}.\n", array('fg' => 'red'));
            echo $this->formatter->format("\nSolution: Possible that's you first migration. \n\n", array('fg' => 'yellow'));
            return false;
        }

        $dbImporter = new Doctrine_Import_Schema();
        $schema = $dbImporter->buildSchema($this->_dbFileName, 'yml');
        
        $schema = $this->addDbIndexes($schema);
        $schema = $this->addConstrainst($schema);
        $schema = $this->ksortRecursive($schema);
        return $schema;
    }

    /**
     * Create temporary tables from current schema for found compatibility
     *
     * @param string $schemaFiles
     * @return string
     */
    protected function createMigrationTables($schemaFiles)
    {
        
        $tmpDirectory = sys_get_temp_dir().'/'.time().'/';
        Doctrine_Lib::makeDirectories($tmpDirectory);
        $migDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'tmp_doctrine_models';

        set_include_path(
                get_include_path().
                PATH_SEPARATOR.$tmpDirectory.
                PATH_SEPARATOR.$tmpDirectory.'generated'.
                PATH_SEPARATOR.$migDir
        );

        $tmpSchemaFile = $tmpDirectory.'schema.yml';
        $allSchemas = array();

        foreach($schemaFiles as $file) {
            $schema = $this->_ymlParser->loadData($file);
            if(is_array($schema))
                foreach($schema as $key=>$value){
                    $value['tableName'] = $this->_slysMigPrefix.$value['tableName'];
                    $newName = 'Temp'.$this->_filter->filter($key);
                    $this->_correspondence[$newName] = $key;
                    $allSchemas[$newName] = $value;
                }
        }
        
        $this->_ymlParser->dumpData($allSchemas, $tmpSchemaFile);

        $file = file_get_contents($tmpSchemaFile);
        foreach($this->_correspondence as $key=>$value)
            $file = str_replace($value, $key, $file);

        file_put_contents($tmpSchemaFile, $file);

        $generateOptions = array(
            'generateBaseClasses'=>true,
            'baseClassPrefix'=>'TempBase_',
            'generateTableClasses'=>true,
            'pearStyle'=>true );

        Doctrine_Core::generateModelsFromYaml( $tmpSchemaFile, $tmpDirectory, $generateOptions );

        Doctrine_Core::createTablesFromModels($tmpDirectory);


        @Doctrine_Core::generateYamlFromDb( $this->_curFileName, array('doctrine') );

        $schema = $this->_ymlParser->loadData($this->_curFileName);

        foreach($schema as $key=>$value) {
            if(!strstr($key, 'SlysMigration')) {
                unset($schema[$key]);
            }
        }

        $schema = $this->addDbIndexes($schema);
        $schema = $this->addConstrainst($schema);

        $this->_ymlParser->dumpData($schema, $this->_curFileName);

        $this->dropTmpTables($tmpDirectory);
        Doctrine_Lib::removeDirectories($tmpDirectory);

        return $this->_curFileName;
    }

    /**
     * Drop temporary tables from DB
     * @param string $tmpDirectory
     */
    protected function dropTmpTables($tmpDirectory)
    {
        $processor = new Doctrine_Migration_Process($this->_migration);
        $dropModels = Doctrine_Core::loadModels($tmpDirectory);
        foreach($dropModels as $model) {
            $dropModel = new $model();
            if($dropModel instanceof Doctrine_Record) {
                $dropTable = $dropModel->getTable();
                $tableDefinition = $dropTable->getExportableFormat();
                if(!empty($tableDefinition['options']['foreignKeys']))
                    foreach($tableDefinition['options']['foreignKeys'] as $foreignKey) {
                        $processor->processDroppedForeignKey(array(
                            'tableName'=>$tableDefinition['tableName'],
                            'definition'=>array('name'=>$foreignKey['name'])));
                }

            }

        }

        foreach($dropModels as $model) {
            $dropModel = new $model();
            if($dropModel instanceof Doctrine_Record) {
                $dropTable = $dropModel->getTable();
                $tableDefinition = $dropTable->getExportableFormat();
                $processor->processDroppedTable($tableDefinition);
            }
            unset($dropModel);
        }
    }

    /**
     * Remove unused elements from shema, which insert troubles
     * @param array $data
     * @return array
     */
    protected function removeUnusedElements($data)
    {
        if(empty($data))
            return $data;
        
        if(isset($data['MigrationVersion'])) {
            unset($data['MigrationVersion']);
        }
        
        foreach($data as $key=>$value)
        if(isset($data[$key]['connectionClassName']))
            unset($data[$key]['connectionClassName']);
        return $data;
    }

    /**
     * Add indexes to schema, because standard Doctrine method
     * not export schema with indexes
     * @param array $schemas
     * @return array
     */
    protected function addDbIndexes($schemas)
    {
        $performTables = array_keys($schemas);

        foreach ($this->_connection->getCurrentConnection()->import->listTables() as $table) {

            $model = ucfirst($this->_filter->filter($table));
            if(in_array($model, $performTables)) {
                $keyName = 'Key_name';
                $nonUnique = 'Non_unique';
                $indexSuffix = '_idx';

                $table = $this->_connection->getCurrentConnection()->quoteIdentifier($table, true);
                $query = 'SHOW INDEX FROM '.$table;
                $indexes = $this->_connection->getCurrentConnection()->fetchAssoc($query);
                $result = array();

                foreach ($indexes as $key => $indexData) {
                    
                    $indexName = $indexData[$keyName];

                    if (strstr($indexName, $indexSuffix)) {
                        $indexName = str_replace($indexSuffix, '', $indexName);                        
                    } 
                    
                    if($indexName != 'PRIMARY') {
                        $result[$indexName]['fields'][] = $indexData['Column_name'];
                        if (empty($indexData[$nonUnique]))
                            $result[$indexName]['type'] = 'unique';
                    }

                    if (!empty($result)) {
                        $schemas[$model]['indexes'] = $result;
                    }
                }
            }
        }

        return $schemas;
    }

    /**
     * Sorting array by keys recursive
     * @param array $array
     * @return array
     */
    protected function ksortRecursive(array $array)
    {
        foreach($array as $key=>$value) {
            if(is_array($value)) {
                $array[$key] = $this->ksortRecursive($value);
            }
        }
        ksort($array);
        return $array;
    }

    /**
     * Add constraints to schema reations, because standard Doctrine method
     * not export schema with indexes
     * @param array $schema
     * @return array
     */
    protected function addConstrainst($schema)
    {
        foreach($schema as $key=>$value) {

            $trelations = $this->listTableRelations($value['tableName']);

            foreach($trelations as $trelation) {

                foreach($value['relations'] as $rkey=>$srelation) {

                    if($srelation['local'] == $trelation['local']
                            && $srelation['foreign'] == $trelation['foreign']
                            && $srelation['type'] == 'one' ) {

                        if(!empty($trelation['onDelete']) && $trelation['onDelete'] != 'RESTRICT') {
                            $schema[$key]['relations'][$rkey]['onDelete'] = $trelation['onDelete'];
                        }
                        if(!empty($trelation['onUpdate']) && $trelation['onUpdate'] != 'RESTRICT') {
                            $schema[$key]['relations'][$rkey]['onUpdate'] = $trelation['onUpdate'];
                        }

                    }
                }
            }

        }
        
        return $schema;
    }

    /**
     * Return list of relations with constraints
     *
     * @param string $tableName
     * @return array
     */
    protected function listTableRelations($tableName)
    {
        $relations = array();
        $sql = "SELECT kcu.column_name, kcu.REFERENCED_TABLE_NAME, rc.UPDATE_RULE, rc.DELETE_RULE, kcu.REFERENCED_COLUMN_NAME ".
            " FROM information_schema.key_column_usage kcu".
            " INNER JOIN information_schema.REFERENTIAL_CONSTRAINTS rc ON kcu.CONSTRAINT_NAME = rc.CONSTRAINT_NAME".
            " WHERE kcu.table_name = '" . $tableName . "' AND kcu.table_schema = '" . $this->_connection->getCurrentConnection()->getDatabaseName() .
                "' and kcu.REFERENCED_COLUMN_NAME is not NULL";

        $results = $this->_connection->getCurrentConnection()->fetchAssoc($sql);

        foreach ($results as $result)
        {
            $result = array_change_key_case($result, CASE_LOWER);
            $relations[] = array('table'   => $result['referenced_table_name'],
                                 'local'   => $result['column_name'],
                                 'foreign' => $result['referenced_column_name'],
                                 'onDelete' => $result['delete_rule'],
                                 'onUpdate' => $result['update_rule']

                                );
        }

        return $relations;
    }

    /**
     * Create empty table, used for migration,
     * because Doctrine can't migrate with empty previous schema
     *
     * @return array
     */
    protected function createEmptyDbSchema()
    {
        $schema = array('SlysTMP'=>array('tableName'=>'slys_tmp'));
        return $schema;
    }

}
