<?php
/**
 *	SlyS
 * @author     Pavel Galaton <pavel.galaton@gmail.com>
 *
 * @version    $Id: TreeData.php 839 2010-12-21 10:54:20Z deeper $
 */
class Slys_Controller_Action_Helper_TreeData extends Zend_Controller_Action_Helper_Abstract
{
	public function prepareDataFromNavigation(Zend_Navigation_Container $navigation, $idField, $labelField)
	{
		$navData = $navigation->toArray();
		$navData = $this->_renamePagesToChildren($navData);

		return new Zend_Dojo_Data($idField, $navData, $labelField);
	}

	protected function _renamePagesToChildren(array $data)
	{
		$result = array();

		foreach ($data as $item) {
			if (empty($item['id']))
				$item['id'] = str_replace(array('.', ' '), '', microtime());

			if (!empty($item['pages'])) {
				$pages = $item['pages'];
				unset($item['pages']);

				$item['children'] = $this->_renamePagesToChildren($pages);
			}

			$result[] = $item;
		}

		return $result;
	}

	public function direct()
    {
        return $this;
    }

	public function prepareDojoData(Doctrine_Tree_NestedSet $treeObject, $idField, $labelField)
	{
		return new Zend_Dojo_Data($idField , $treeObject->fetchTree(array(),Doctrine_Core::HYDRATE_ARRAY_HIERARCHY), $labelField);
	}
}