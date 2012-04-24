<?php
/**
 * Slys
 * @author Serghei Ilin <criolit@gmail.com>
 * @version $Id$
 */

/**
 * Sysmap model class. Provides as singleton
 * @throws Zend_Exception
 */
class Sysmap_Model_Map
{
    /**
     * @var Sysmap_Model_Map
     */
    protected static $_instance = null;

    /**
     * @var bool
     */
    protected $_reindexed = false;

    /**
     * @var string
     */
    protected $_mcaIndexPath;

    private static $hashParentsCache = array();
    /**
     * @var array
     */
    protected $_requestActiveElementsCache = array();

    protected function __construct()
    {
        $applicationPath = str_replace('\\','/', realpath(APPLICATION_PATH));
        $this->_mcaIndexPath = $applicationPath.'/../data/cache/mca.index';
    }

    public static function getInstance()
    {
        if (self::$_instance === null)
            self::$_instance = new self;

        return self::$_instance;
    }

    /**
     * Convert MCA reprezentation for Sysmap
     *
     * @param $mca array
     * @return string
     */
    public function formatMcaName(array $mca)
    {
        return strtolower(
            (empty($mca['module']) ? '*' : $mca['module'])
            . '.' .
            (empty($mca['controller']) ? '*' : $mca['controller'])
            . '.' .
            (empty($mca['action']) ? '*' : $mca['action'])
        );
    }

    /**
     * Generate hash for item and set the value to the hash member
     * @param Sysmap_Model_Mapper_Sysmap $item
     * @return void
     */
    protected function _generateHash(Sysmap_Model_Mapper_Sysmap $item)
    {
        if (empty($item) === false)
            $item->hash = $item->level . '-' . md5(
                $item->mca . '#' .
                $item->form_name . '#' .
                print_r($item->params, true)
            );
    }

    /**
     * Parse MCA format to array with module-controller-action
     * @return array|null
     */
    public function parseMcaFormat($mca)
    {
        if (empty($mca) === true or is_string($mca) === false)
            return null;

        $parts = explode('.', $mca);
        if (count($parts) < 3)
            return null;

        $return['module'] = $parts[0] == '*' ? 'default' : $parts[0];
        $return['controller'] = $parts[1] == '*' ? 'index' : $parts[1];
        $return['action'] = $parts[2] == '*' ? 'index' : $parts[2];

        return $return;
    }

    /**
     * Returns formatted absolute path to the
     *
     * Returns false if path can't be found
     *
     * @throws Zend_Exception
     * @param string $mca
     * @return string|bool
     */
    public function formatPathFromMca($mca)
    {
        if (empty($mca) === true or is_string($mca) === false)
            return null;

        if($mca == '*.*.*')
            return false;

        $mcaParts = explode('.',$mca);
        $mcaParts['module'] = ($mcaParts[0] == '*') ? NULL : $mcaParts[0];
        $mcaParts['controller'] = ($mcaParts[1] == '*') ? NULL : $mcaParts[1];
        $mcaParts['action'] = ($mcaParts[2] == '*') ? NULL : $mcaParts[2];

        if($mca != $this->formatMcaName($mcaParts))
            throw new Zend_Exception('Invalid MCA provided');

        $applicationPath = str_replace('\\','/',realpath(APPLICATION_PATH));

        if(!empty($mcaParts['controller'])) {
            $frontController = Zend_Controller_Front::getInstance();
            $controllerClassName = $frontController->getDispatcher()->formatControllerName($mcaParts['controller']);
            $controllerFileName = $frontController->getDispatcher()->classToFilename($controllerClassName);

            return str_replace(
                $applicationPath,
                '',
                str_replace('\\','/',realpath(Zend_Controller_Front::getInstance()->getControllerDirectory($mcaParts['module']).DIRECTORY_SEPARATOR.$controllerFileName))
            );
        } else {
            return str_replace($applicationPath,'',str_replace('\\','/',realpath(Zend_Controller_Front::getInstance()->getModuleDirectory($mcaParts['module']))));
        }
    }

    /**
     * @param  $moduleName
     */
    public function addModule($moduleName, $path, $title = null, $description = null)
    {
        $rootNode = Sysmap_Model_DbTable_Sysmap::getInstance()->findOneBy('mca','*.*.*');

        if (empty($rootNode))
            throw new Zend_Exception('Can not find root of the sysmap!');

        $newItem = false;
        $mapItem = Sysmap_Model_DbTable_Sysmap::getInstance()->findOneBy('mca', $this->formatMcaName(array('module' => $moduleName)));

        if (empty($mapItem)) {
            $newItem = true;
            $mapItem = new Sysmap_Model_Mapper_Sysmap();
            $mapItem->mca = $this->formatMcaName(array('module' => $moduleName));
        }

        $mapItem->title = empty($title) ? $mapItem->mca : $title;
        $mapItem->description = $description;
        $mapItem->path = dirname($path);
        $this->_generateHash($mapItem);
        $mapItem->save();

        if ($newItem) {
            if (empty($mapItem) === false) {
                $mapItem->getNode()->insertAsLastChildOf($rootNode);
                $this->_generateHash($mapItem);
                $mapItem->save();
            }
            else
                throw new Zend_Exception('Unable to assign new item to not existing root element!');
        }
    }

    /**
     * @param  $moduleName
     * @param  $controllerName
     */
    public function addController($moduleName, $controllerName, $path, $title = null, $description = null)
    {
        $moduleRoot = Sysmap_Model_DbTable_Sysmap::getInstance()->findOneBy('mca', $this->formatMcaName(array('module' => $moduleName)));

        if (empty($moduleRoot))
            throw new Zend_Exception('Can not find module root entry!');

        $newItem = false;
        $mapItem = Sysmap_Model_DbTable_Sysmap::getInstance()->findOneBy('mca', $this->formatMcaName(array('module' => $moduleName, 'controller' => $controllerName)));

        if (empty($mapItem)) {
            $newItem = true;
            $mapItem = new Sysmap_Model_Mapper_Sysmap();
            $mapItem->mca = $this->formatMcaName(array('module' => $moduleName, 'controller' => $controllerName));
        }

        $mapItem->title = empty($title) ? $mapItem->mca : $title;
        $mapItem->description = $description;
        $mapItem->path = $path;
        $this->_generateHash($mapItem);
        $mapItem->save();

        if ($newItem) {
            if (empty($mapItem) === false) {
                $mapItem->getNode()->insertAsLastChildOf($moduleRoot);
                $this->_generateHash($mapItem);
                $mapItem->save();
            }
            else
                throw new Zend_Exception('Unable to assign new item to not existing root element!');
        }
    }

    /**
     * @param  $moduleName
     * @param  $controllerName
     * @param  $actionName
     */
    public function addAction($moduleName, $controllerName, $actionName, $path, $formClass = null, $title = null, $description = null)
    {
        $controllerRoot = Sysmap_Model_DbTable_Sysmap::getInstance()->findOneBy('mca', $this->formatMcaName(array('module' => $moduleName, 'controller' => $controllerName)));

        if (empty($controllerRoot))
            throw new Zend_Exception('Can not find controller root element!');

        $newItem = false;
        $mapItem = Sysmap_Model_DbTable_Sysmap::getInstance()->findOneBy('mca', $this->formatMcaName(array('module' => $moduleName, 'controller' => $controllerName, 'action' => $actionName)));

        if (empty($mapItem)) {
            $newItem = true;
            $mapItem = new Sysmap_Model_Mapper_Sysmap();
            $mapItem->mca = $this->formatMcaName(array('module' => $moduleName, 'controller' => $controllerName, 'action' => $actionName));
        }

        $mapItem->form_name = $formClass;
        $mapItem->title = empty($title) ? $mapItem->mca : $title;
        $mapItem->description = $description;
        $mapItem->path = $path;
        $this->_generateHash($mapItem);
        $mapItem->save();

        if ($newItem) {
            if (empty($mapItem) === false) {
                $mapItem->getNode()->insertAsLastChildOf($controllerRoot);
                $this->_generateHash($mapItem);
                $mapItem->save();
            }
            else
                throw new Zend_Exception('Unable to assign new item to not existing root element!');
        }
    }

    /**
     * Returns nested set of the sysmap
     * @param null $fields
     * @return Doctrine_Tree
     */
    public function getMapTree($fields = null)
    {
        $tree = Doctrine_Core::getTable('Sysmap_Model_Mapper_Sysmap')->getTree();
    	$baseAlias = $tree->getBaseAlias();
    	$select = '';

    	if ($fields === null)
    		$select = $baseAlias . '.id,' . $baseAlias . '.title';
    	else {
    		foreach ($fields as $field)
    			$select .= $baseAlias . '.' . $field . ',';

    		$select = substr($select, 0, -1);
    	}

		$tree->setBaseQuery(
			Doctrine_Core::getTable('Sysmap_Model_Mapper_Sysmap')
						   ->createQuery($baseAlias)
						   ->select($select)
		);

    	return $tree;
    }

    /**
     * Converts camel-case method name to dashed url version (someActionTestAction to some-action-test)
     *
     * @param  string $actionName
     * @return string
     */
    protected function _urlActionName($actionName)
    {
        return strtolower( preg_replace('/([A-Z]+)/', '-\1', preg_replace('/(Action)$/', '', $actionName) ) );
    }

    /**
     * $paths variable can be DirectoryIterator object
     * or an array of DirectoryIterator objects
     *
     * @param DirectoryIterator|array $paths
     * @return void
     */
    public function addToMap($paths)
    {
        if (empty($paths))
            return;

        if ( is_array($paths) === false )
            $paths = array($paths);

        $mapMethods = array();

        foreach($paths as $path) {
            require_once APPLICATION_PATH. $path;
            $reflectionFile = new Zend_Reflection_File(APPLICATION_PATH . $path);
            $class = $reflectionFile->getClass();

            $classParts = explode('_', $class->getName());

            $module = 'default';

            if (count($classParts) == 1)
                $controllerName = strtolower(str_replace('Controller', '', $classParts[0]));
            else {
                $module = $classParts[0];
                $controllerName = strtolower(str_replace('Controller', '', $classParts[1]));
            }

            $this->addModule($module, $path);

            $controllerDocTitle = $controllerName;
            $controllerDocDescription = '';

            try {
                $controllerDoc = $class->getDocblock();

                $controllerDocTitle = $controllerDoc->getShortDescription();
                $controllerDocDescription = $controllerDoc->getLongDescription();
            }
            catch(Zend_Reflection_Exception $exception) {
            }

            // adding module-controller
            $this->addController($module, $controllerName, $path, $controllerDocTitle, $controllerDocDescription);

            $tmpMapMethods = Sysmap_Model_DbTable_Sysmap::getInstance()->findActions($module, $controllerName, array(), Doctrine_Core::HYDRATE_ARRAY);

            foreach($tmpMapMethods as $method)
                $mapMethods[$method['mca']] = $method;

            unset($tmpMapMethods);

            foreach ($class->getMethods() as $method) {
                $methodName = $method->getName();

                if (!preg_match('/.+Action$/', $methodName))
                    continue;

                $title = '';
                $formClass = '';
                $description = '';

                try {
                    $docBlock = $method->getDocblock();
                    $formTag = $docBlock->getTag('paramsform');

                    if (!empty($formTag))
                        $formClass = trim($formTag->getDescription());

                    $title = trim($docBlock->getShortDescription());
                    $description = trim($docBlock->getLongDescription());
                }
                catch(Zend_Reflection_Exception $exception) {
                }

                // add module-controller-action + form name
                $this->addAction($module, $controllerName, $this->_urlActionName($methodName), $path, $formClass, $title, $description);

                $mcaKey = $this->formatMcaName(array('module' => $module, 'controller' => $controllerName, 'action' => $this->_urlActionName($methodName)));

                if (isset($mapMethods[$mcaKey]))
                    unset($mapMethods[$mcaKey]);
            }

            if (empty($mapMethods) === false) {
                Sysmap_Model_DbTable_Sysmap::getInstance()->deleteRecords(array_values($mapMethods));
                unset($mapMethods);
            }
        }
    }

    /**
     * Reindex MCA
     * @return void
     */
    public function reindexMCA()
    {
        if ($this->_reindexed === true)
            return;

        $rootElement = Sysmap_Model_DbTable_Sysmap::getInstance()->getRootElement();
        $lastIndexedDate = (int)strtotime($rootElement->index_date);

        $applicationPath = str_replace('\\','/', realpath(APPLICATION_PATH));
        $indexFile = $this->_mcaIndexPath;

        $doReindex = false;

        $pathsInIndex = $foundPaths = $addPaths = $resultIndex = $removedPaths = array(
            'controllers' => array(),
            'controllers_folders' => array(),
        );

        if( file_exists($indexFile) ) {
            $pathsInIndex = require_once $indexFile;
            $indexDate = $pathsInIndex['index_date'];
        } else {
            $indexDate = 1;
            $doReindex = true;
        }

        if($indexDate != $lastIndexedDate) { //full reindex case
            $pathsInIndex = array(
                'controllers' => array(),
                'controllers_folders' => array(),
            );

            if($lastIndexedDate > 0) { //index in database exists
                $indexDate = $lastIndexedDate; //restoring indexation date from database
                $oldIndexControllers = Sysmap_Model_DbTable_Sysmap::getInstance()->findControllers();

                /** @var $module Sysmap_Model_Mapper_Sysmap */
                foreach($oldIndexControllers as $mapItem) {
                    if(empty($mapItem->path))
                        continue;

                    $pathsInIndex['controllers'][] = $mapItem->path;
                }

                $oldIndexModules = Sysmap_Model_DbTable_Sysmap::getInstance()->findModules();

                /** @var $module Sysmap_Model_Mapper_Sysmap */
                foreach($oldIndexModules as $mapItem) {
                    if(empty($mapItem->path))
                        continue;

                    $pathsInIndex['controllers_folders'][] = $mapItem->path;
                }
            }
        }



        foreach(Zend_Controller_Front::getInstance()->getControllerDirectory() as $moduleName => $controllersDirectory) {
            if (file_exists($controllersDirectory) === false)
                continue;

            $controllersDirectoryRelPath = str_replace($applicationPath,'',str_replace('\\','/',realpath($controllersDirectory)));

            if(!in_array($controllersDirectoryRelPath,$pathsInIndex['controllers_folders'])) {
                $addPaths['controllers_folders'][] = $controllersDirectoryRelPath;
            } else {
                $foundPaths['controllers_folders'][] = $controllersDirectoryRelPath;
            }

            foreach (new DirectoryIterator($controllersDirectory) as $fileInfo) {
                if( $fileInfo->isFile() ) {
                    $pathName = str_replace($applicationPath,'',str_replace('\\','/',realpath($fileInfo->getPathname())));
                    if(
                        !in_array($pathName,$pathsInIndex['controllers'])
                        OR
                        $fileInfo->getMTime() > $indexDate
                    ) {
                        $addPaths['controllers'][] = $pathName;
                    } else {
                        $foundPaths['controllers'][] = $pathName;
                    }
                }
            }
        }

        $resultIndex['controllers_folders'] += $foundPaths['controllers_folders'];
        $resultIndex['controllers']         += $foundPaths['controllers'];
        $resultIndex['controllers_folders'] += $addPaths['controllers_folders'];

        $resultIndex['controllers'] = array_merge($resultIndex['controllers'], $addPaths['controllers']);

        $removedPaths['controllers']         = array_diff($pathsInIndex['controllers'],$resultIndex['controllers']);
        $removedPaths['controllers_folders'] = array_diff($pathsInIndex['controllers_folders'],$resultIndex['controllers_folders']);

        if (empty($removedPaths['controllers']) === false) {
            foreach($removedPaths['controllers'] as $path) {
                $item = Sysmap_Model_DbTable_Sysmap::getInstance()->findOneBy('path', $path);

                if (empty($item) === false)
                    $item->getNode()->delete();
            }

            $doReindex = true;
        }

        if (empty($removedPaths['controllers_folders']) === false) {
            foreach($removedPaths['controllers_folders'] as $path) {
                $item = Sysmap_Model_DbTable_Sysmap::getInstance()->findOneBy('path', $path);

                if (empty($item) === false)
                    $item->getNode()->delete();
            }

            $doReindex = true;
        }

        if(empty($addPaths['controllers']) === false) {
            $this->addToMap($addPaths['controllers']);
            $doReindex = true;
        }

        if($doReindex) {
            $resultIndex['index_date'] = time();

            $rootElement->index_date = date('Y-m-d H:i:s',$resultIndex['index_date']);
            $rootElement->save();

            file_put_contents($indexFile,'<?php return '.var_export($resultIndex,true).';' );
//            chmod($indexFile,0777);
        }

        $this->_reindexed = true;
    }

    /**
     * @throws Zend_Exception
     * @param array $data
     * @return Sysmap_Model_Mapper_Sysmap
     */
    public function addExtend(array $data)
    {
        $oldHash = null;
        $extendUpdated = false;

        if (empty($data) === true)
            throw new Zend_Exception('Can not create an extend! Empty data passed!');

        $mapItem = Sysmap_Model_DbTable_Sysmap::getInstance()->findOneBy('id', $data['sysmap_id']);

        if (empty($mapItem))
            throw new Zend_Exception('The root element you choosed does not exists!');

        if ($mapItem->level != 3)
            throw new Zend_Exception('You can assign extend only to the map item with level equal 3 (to actions)!');

        if (empty($mapItem->form_name) === true)
            throw new Zend_Exception('You can not create extend from action without form!');

        $extend = new Sysmap_Model_Mapper_Sysmap();

        if (empty($data['id']) === false) {
            $extendUpdated = true;
            $extend->assignIdentifier($data['id']);
            $oldHash = $extend->hash;
        }

        unset($data['id']);

        $extend->fromArray($data);
        $extend->save();

        $extend->getNode()->insertAsLastChildOf($mapItem);

        $extend->mca = $mapItem->mca;
        $this->_generateHash($extend);
        $extend->mca = null;
        $extend->save();

        if ($extendUpdated) {
            $params = array('identifier' => $oldHash);

            if (empty($oldHash) === false)
                $params['new_identifier'] = $extend->hash;

            Slys_Api::getInstance()->notify(null, 'sysmap.item-updated', $params);
        }

        return $extend;
    }

    /**
     * Return item or null by $hash
     * @param  $hash
     * @return Doctrine_Record
     */
    public function getItemByHash($hash)
    {
        if (empty($hash) === false)
            return Sysmap_Model_DbTable_Sysmap::getInstance()->findOneBy('hash', $hash);

        return null;
    }

    /**
     * Returns form tree element with filled map values
     * It automatically makes reindex to provide fresh information
     *
     * @return Slys_Form_Element_Tree
     */
    public function getMapTreeElement()
    {
        $this->reindexMCA();

        $sysmapTree = $this->getMapTree(array('id', 'title', 'hash', 'mca', 'level'))->fetchTree(array('id' => 1), Doctrine_Core::HYDRATE_ARRAY_HIERARCHY);

        $tree = new Slys_Form_Element_Tree('sysmap_id');
        $tree->setValueKey('hash')
             ->setTitleKey('title')
             ->setAllowEmpty(false)
             ->setRequired(true);

        $tree->setLabel('sysmap_tree');
        $tree->addMultiOptions($sysmapTree);

        return $tree;
    }

    /**
     * Return currently active sysmap items based on current request or request passed as a parameter
     * @param null|Zend_Controller_Request_Abstract $customRequest
     * @return null|Doctrine_Collection
     */
    public function getActiveItems(Zend_Controller_Request_Abstract $customRequest = null)
    {
        $collection = null;

        if ($customRequest !== null)
            $request = $customRequest;
        else
            $request = Zend_Controller_Front::getInstance()->getRequest();

        if (empty($request))
            return null;

        $currentMcaName = array();

        foreach($request->getParams() as $key=>$value) {
            if(!is_string($value))
                continue;

            $currentMcaName[] = $key.'=>'.$value;
        }

        $currentMcaName = implode(',',$currentMcaName);

        if (empty($this->_requestActiveElementsCache[$currentMcaName]) === false)
            return $this->_requestActiveElementsCache[$currentMcaName];

        $this->reindexMCA();

        $mca = array(
            'module' => $request->getModuleName(),
            'controller' => $request->getControllerName(),
            'action' => $request->getActionName()
        );

        /** @var Doctrine_Collection $currentMcaCollection */
        $currentMcaCollection = Sysmap_Model_DbTable_Sysmap::getInstance()->findBy('mca', $this->formatMcaName($mca));

        $mapItem = $currentMcaCollection->get(0);

        if ( $mapItem->exists() ) {
            $collection = $mapItem->getNode()->getAncestors();

            if (empty($collection) === false)
                $collection->add($mapItem);
            else
                $collection = $currentMcaCollection;

            //getting extend
            if(false === empty($mapItem->form_name)) {
                $formName = $mapItem->form_name;
                $paramsForm = new $formName;
                $paramsForm->populate($request->getParams());
                $extend = Sysmap_Model_DbTable_Sysmap::getInstance()->findOneBy('params', serialize($paramsForm->getValues(true)));
                if(false === empty($extend) && $extend->exists()) {
                    $collection->add($extend);
                }
            }
        }

        if (empty($collection) === false) {
            $mcaConditions[] = '*.' . $mca['controller'] . '.*';
            $mcaConditions[] = '*.' . $mca['controller'] . '.' . $mca['action'];
            $mcaConditions[] = '*.*.' . $mca['action'];
            $mcaConditions[] = $mca['module'] . '.*.' . $mca['action'];

            /** @var Doctrine_Collection $patterns */
            $patterns = Doctrine_Query::create()->select()
                                                ->from('Sysmap_Model_Mapper_Sysmap')
                                                ->whereIn('mca', $mcaConditions)
                                                ->execute();

            if (empty($patterns) === false and $patterns->count() > 0)
                $collection->merge($patterns);
        }

        $this->_requestActiveElementsCache[$currentMcaName] = $collection;

        return $collection;
    }

    public function getItemParentsByHash($hash)
    {
        if(empty(self::$hashParentsCache[$hash])) {
            $currentCollection = Sysmap_Model_DbTable_Sysmap::getInstance()->findBy('hash', $hash);
            $mapItem = $currentCollection[0];

            $collection = null;

            if ($mapItem->exists()) {

                $collection = $mapItem->getNode()->getAncestors();

                if (empty($collection) === false)
                    $collection->add($mapItem);
                else
                    $collection = $currentCollection;
            }

            self::$hashParentsCache[$hash] = $collection;
        }

        return self::$hashParentsCache[$hash];
    }

    public function getAllMca()
    {
        $mcaRecords = Sysmap_Model_DbTable_Sysmap::getInstance()->getNonEmtyMca();

        foreach($mcaRecords as $record) {
            $mca =  explode('.', $record->mca);
            $index['modules'][$mca[0]] = $mca[0];
            $index['controllers'][$mca[1]] = $mca[1];
            $index['actions'][$mca[2]] = $mca[2];
        }

        return $index;
    }

    /**
     * @throws Zend_Exception
     * @param array $data
     * @return Sysmap_Model_Mapper_Sysmap
     */
    public function addExtendPattern(array $data)
    {
        $oldHash = null;
        $extendUpdated = false;

        if (empty($data) === true)
            throw new Zend_Exception('Can not create an extend! Empty data passed!');

        $mca = sprintf('%s.%s.%s', $data['mca_module'], $data['mca_controller'], $data['mca_action']);

        if ($mca == '*.*.*')
            throw new Zend_Exception('You can not create pattern *.*.*!');

        if (
            ($data['mca_module'] != '*' and $data['mca_controller'] == '*' and $data['mca_action'] != '*') === false
            and
            ($data['mca_module'] == '*' and $data['mca_controller'] != '*' and $data['mca_action'] != '*') === false
            and
            ($data['mca_module'] == '*' and $data['mca_controller'] != '*' and $data['mca_action'] == '*') === false
            and
            ($data['mca_module'] == '*' and $data['mca_controller'] == '*' and $data['mca_action'] != '*') === false
        )
            throw new Zend_Exception('The pattern you are trying to create ('.$mca.') already listed in sysmap!');

        $mapItem = Sysmap_Model_DbTable_Sysmap::getInstance()->getTree()->fetchRoot();

        if (empty($mapItem))
            throw new Zend_Exception('The root element you choosed does not exists!');

        $extend = new Sysmap_Model_Mapper_Sysmap();

        if (empty($data['id']) === false) {
            $extendUpdated = true;
            $extend->assignIdentifier($data['id']);
            $oldHash = $extend->hash;
        }

        unset($data['id']);

        $extend->fromArray($data);
        $extend->save();

        $extend->getNode()->insertAsLastChildOf($mapItem);
        $extend->mca = $mca;
        $extend->is_pattern = true;

        $this->_generateHash($extend);

        $extend->save();

        if ($extendUpdated) {
            $params = array('identifier' => $oldHash);

            if (empty($oldHash) === false)
                $params['new_identifier'] = $extend->hash;

            Slys_Api::getInstance()->notify(null, 'sysmap.item-updated', $params);
        }

        return $extend;
    }
}