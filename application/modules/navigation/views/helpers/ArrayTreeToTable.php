<?php
/**
 * Slys
 *
 * Main module navigation module
 *
 * @author     Serghei Ilin <criolit@gmail.com>
 * @version    $Id: ArrayTreeToTable.php 1177 2011-02-06 12:11:53Z criolit $
 */
class Navigation_View_Helper_ArrayTreeToTable extends Zend_View_Helper_Abstract
{
	/**
	 * Render admin submenu. If no navigation container is added helper will automatically gets the current
	 * navigation using Esb. Conditions allows to filter what navigation entries can be displayed
	 *
	 * @param Zend_Navigation $navigation
	 * @param array|null $conditions
	 * @param int $depth
	 * @param string $className
	 *
	 * @return string
	 */
	public function arrayTreeToTable(array $tree, $tableClass = 'admin-table')
	{
		$output  = '<table class="' . $tableClass . '">';
		$output .= $this->view->render('arrayTreeTableHead.phtml');

		foreach ($tree as $menu)
			$output .= $this->_itemRows($menu);

		return $output . '</table>';
	}

	protected function _itemRows($rows)
	{
		$output = '';

        if (is_array($rows) === false)
            return $output;

		foreach ($rows as $item) {
			$this->view->arrayTreeItem = $item;
			$output .= $this->view->render('arrayTreeTableRow.phtml');

			if (!empty($item['__children']))
				$output .= $this->_itemRows($item['__children']);
		}

		return $output;
	}
}