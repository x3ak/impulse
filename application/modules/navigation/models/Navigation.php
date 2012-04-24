<?php
/**
 * Slys
 *
 * Main module navigation module
 *
 * @author     Serghei Ilin <criolit@gmail.com>
 * @version    $Id: Navigation.php 1176 2011-02-04 16:05:59Z criolit $
 */
class Navigation_Model_Navigation
{
	/**
	 * Defines that navigation item referers to an external resource
	 * @var string
	 */
    const TYPE_EXTERNAL = 'external';

    /**
     * Defines that navigation type will contain controller and action keys in array of response
     * @var string
     */
    const TYPE_PROGRAMMATIC = 'programmatic';

    /**
     * Defines that navigation type is root of the navigation
     * @var string
     */
    const TYPE_NAVIGATION_ROOT = 'menu';

    /**
     * @var Navigation_Model_Navigation
     */
    protected static $_instance = null;

    /**
     * @var boolean
     */
    protected $_cacheEnabled = false;

    /**
     * @var Zend_Cache_Core
     */
    protected $_cache = null;

    /**
     * @var string
     */
    protected $_cacheName = 'navigation_full_navigation';

    protected function __construct()
    {
        $options = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('modules')->navigation->getOptions();

        if (empty($options) === false) {
            $this->_cacheEnabled = (boolean)$options['cache']['enabled'];

            $this->_cache = Zend_Cache::factory(
                $options['cache']['frontend']['name'],
                $options['cache']['backend']['name'],
                $options['cache']['frontend']['options'],
                $options['cache']['backend']['options']
            );
        }
    }

    public static function getInstance()
    {
        if (self::$_instance === null)
            self::$_instance = new self;

        return self::$_instance;
    }

    /**
     * Returns NestedSet with all navigation items
     *
     * @param array $fields List of table fields
     * @return Doctrine_Tree_NestedSet
     */
    public function getStructureTree($fields = null)
    {
    	$tree = Doctrine_Core::getTable('Navigation_Model_Mapper_Item')->getTree();
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
			Doctrine_Core::getTable('Navigation_Model_Mapper_Item')
						   ->createQuery($baseAlias)
						   ->select($select)
		);

    	return $tree;
    }

    /**
     * Get navigation item
     * @param int $id
     * @return Navigation_Model_Mapper_Item
     */
    public function getItem($id)
    {
    	if ($id === null)
    		return null;

    	return Navigation_Model_DbTable_Item::getInstance()->find($id);
    }

    /**
     * Save normal item in navigation tree
     * @param array $values
     */
    public function saveLeafItem(array $values)
    {
        if (empty($values))
            return ;

        $newNode = true;
        $oldParentId = null;

        $childNode = new Navigation_Model_Mapper_Item();
        $rootNode = Navigation_Model_DbTable_Item::getInstance()->find($values['parent_id']);

        if (!empty($values['id'])) {
            $childNode->assignIdentifier($values['id']);
            $newNode = false;
            $oldParentId = $childNode->getNode()->getParent()->id;
        }

        unset($values['id']);

        if (empty($values['options']) === false) {
            foreach($values['options'] as $key => $value)
                $values[$key] = $value;

            unset($values['options']);
        }

        $childNode->fromArray($values);
        $childNode->save();

        if ($newNode === true)
            $childNode->getNode()->insertAsLastChildOf($rootNode);
        elseif ($oldParentId != $values['parent_id'])
            $childNode->getNode()->moveAsLastChildOf($rootNode);

        $this->clearCache();
    }

    /**
     * Delete menu node
     * @param $id
     */
    public function deleteItem($id)
    {
    	if (!empty($id)) {
			$menu = Navigation_Model_DbTable_Item::getInstance()->findOneById($id);
			$menu->getNode()->delete();

            $this->clearCache();
		}
    }

    /**
     * Delete menu node by sysmap_identifier
     * @param $id
     */
    public function deleteItemByIdentifier($identifier)
    {
    	if (!empty($identifier)) {
			$menu = Navigation_Model_DbTable_Item::getInstance()->findBy('sysmap_identifier', $identifier);

            foreach($menu as $item)
			    $item->getNode()->delete();

            $this->clearCache();
		}
    }

    /**
     * Get user defined navigation
     * @param int $itemId If null is passed all menus will be as one menu
     * @return Zend_Navigation
     */
    public function getNavigation($itemId = null)
    {
        if ($this->_cache->test($this->_cacheName) === false) {
            $navigation = new Zend_Navigation();

            $roots = Doctrine_Core::getTable('Navigation_Model_Mapper_Item')->getTree()->fetchRoots();
            $this->_formatNavigationPages($roots, $navigation);

            if ($this->_cacheEnabled)
                $this->_cache->save($navigation, $this->_cacheName);
        }
        else
            $navigation = $this->_cache->load($this->_cacheName);

        if ($itemId !== null) {
            $page = $navigation->findOneBy('id', $itemId);

            $navigation = new Zend_Navigation();

            if ($page !== false)
                $navigation->addPage($page);
        }

		return $navigation;
    }

    /**
     * Gets all user defined navigation
     * @param array $root First node for the current tree
     * @param Zend_Navigation_Container $navigation Navigation object which will contain navigation converted from
     * NestedSet
     */
    protected function _formatNavigationPages($root, Zend_Navigation_Container $navigation)
    {
        /** @var $item Navigation_Model_Mapper_Item */
    	foreach ($root as $item) {
            $page = null;

    		if ($item->type == self::TYPE_EXTERNAL) {
    			$page = new Zend_Navigation_Page_Uri();

    			$page->id = $item->id;
    			$page->label = $item->title;
    			$page->uri = $item->external_link;
    		}
    		elseif ($item->type == self::TYPE_PROGRAMMATIC) {
                $page = new Zend_Navigation_Page_Mvc();

                $page->id = $item->id;
                $page->label = $item->title;
                $page->route = $item->route;

                $page->resource = $item->sysmap_identifier;

                if ($page->reset_params === null)
                    $page->reset_params = true;

                /** @var $sysmapItem Sysmap_Model_Mapper_Sysmap */
                $sysmapItem = Slys_Api::getInstance()->request(
                    new Slys_Api_Request($this, 'sysmap.get-item-by-identifier', array(
                        'identifier' => $item->sysmap_identifier
                    ))
                );

				if (empty($sysmapItem) === true)
					continue;

                $sysmapItem = $sysmapItem->getFirst();

                if (empty($sysmapItem) === true)
					continue;

                $mca = $sysmapItem->toRequest();

                $page->module = $mca->getModuleName();
                $page->controller = $mca->getControllerName();
                $page->action = $mca->getActionName();

                $params = $mca->getParams();
                if (!empty($params))
                    $page->params = $mca->getParams();
            }
            elseif($item->type == self::TYPE_NAVIGATION_ROOT) {
                $page = new Zend_Navigation_Page_Mvc();
    			$page->id = $item->id;
    			$page->label = $item->title;
                $page->route = $item->route;
            }

            if ($page === null)
                return;

            $itemNode = $item->getNode();

            if ($itemNode->hasChildren())
                $this->_formatNavigationPages($itemNode->getChildren(), $page);

            $navigation->addPage($page);
    	}
    }

    /**
	 * Create one navigation container from Zend_Navigation_Page_Mvc objects
	 *
	 * @param array $navigations Should contain Zend_Navigation_Page_Mvc objects
	 * @return Zend_Navigation|null
	 */
	public function mergeNavigations($navigations)
	{
		if (!is_array($navigations) or empty($navigations))
			return null;

		$completeNavigation = new Zend_Navigation();

		foreach ($navigations as $navigation) {
			if ($navigation instanceof Zend_Navigation_Page) {
				$completeNavigation->addPage($navigation);
			}
		}

		return $completeNavigation;
	}

    /**
	 * Returns Zend_Navigation with pages which have setted conditions
	 * $conditions have to have the following structure:
	 * [index][page_property] = [page_value]
	 *
	 * @param Zend_Navigation $navigation
	 * @param array $conditions
	 * @return Zend_Navigation|null
	 */
	public function getPagesByConditions(Zend_Navigation $navigation, $conditions, $leaveParents = false)
	{
		if (!is_array($conditions) or empty($conditions))
			return null;

		$resultNavigation = new Zend_Navigation();

		$iterator = new RecursiveIteratorIterator($navigation, RecursiveIteratorIterator::SELF_FIRST);
		// iterating over the navigation pages
		foreach ($iterator as $page) {
			// iterating over the conditions
			foreach ($conditions as $index => $condition) {
				$matched = true;
				// iterating over the condition properties
				foreach ($condition as $property => $value) {
					if (is_array($value)) {
						if (!isset($page->$property) or !in_array($page->$property, $value)) {
							$matched = false;
							break;
						}
					}
					elseif (!isset($page->$property) or $page->$property != $value) {
						$matched = false;
						break;
					}
				}

				if ($matched === true) {
					$resultPage = clone $page;

					if ($leaveParents === false) {
						$resultPage = $resultPage->toArray();
						unset($resultPage['pages']);

						$resultPage = Zend_Navigation_Page::factory($resultPage);
					}

					$resultNavigation->addPage($resultPage);
				}
			}
		}

		return $resultNavigation;
	}

    /**
     * Force clear navigation cache
     * @return void
     */
    public function clearCache()
    {
        if ($this->_cacheEnabled)
            $this->_cache->remove($this->_cacheName);
    }

    /**
     * Used in case when in sysmap identifier is changing
     *
     * @param   string $identifier
     * @param   string $newIdentifier
     * @return  Sysmap_Model_Mapper_Sysmap|null
     */
    public function updateItemHash($identifier, $newIdentifier)
    {


        $navigationItem = Navigation_Model_DbTable_Item::getInstance()->findOneBy('sysmap_identifier', $identifier);

        if (empty($navigationItem) === false) {
            $navigationItem->sysmap_identifier = $newIdentifier;
            $navigationItem->save();
        }

        return $navigationItem;
    }
}