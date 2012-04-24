<?php

/**
 * Slys
 *
 * @author     Evgheni Poleacov <evgheni.poleacov@gmail.com>
 * @version    $Id: Tree.php 839 2010-12-21 10:54:20Z deeper $
 */

/**
 * Doctrine tree extend tools
 */
class Slys_Doctrine_Tree
{
	/**
	 * Return Doctrine_Tree_NestedSet or selected tree
	 * converted to hierarchical data array
	 *
	 * @param Doctrine_Tree_NestedSet $forest
	 * @param int $treeId
	 * @param int $hydration
	 * @return array
	 */
	static function fetchTree(Doctrine_Tree_NestedSet $forest, $treeId = 0, $hydration = Doctrine_Core::HYDRATE_ARRAY_HIERARCHY)
	{
		if(!empty($treeId)) {
			$result = $forest->fetchTree(array('root_id'=>$treeId), $hydration);
		} else {
			$result = array();
			$trees = $forest->fetchRoots();
			$rootField = $forest->getAttribute('rootColumnName');
			foreach($trees as $tree) {
				$current = $forest->fetchTree(array('root_id'=>$tree->$rootField), $hydration);

				if(is_array($current)) {
					$result[] = current($current);
					continue;
				}

				if($current instanceof Doctrine_Collection) {					
					if(is_array($result))
						$result = new Doctrine_Collection($current->getTable());
					$result->merge($current);
				}
			}
			$emptyFilter = new Slys_Filter_Empty();
			$result = $emptyFilter->filter($result);
		}
		return $result;
	}
}