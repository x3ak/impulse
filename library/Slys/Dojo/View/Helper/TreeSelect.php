<?php
/**
 * SlyS
 *
 * @author      Pavel Galaton <pavel.galaton@gmail.com>
 *              Evgheni Poleacov <evgheni.poleacov@gmail.com>
 *
 * @version    $Id: TreeSelect.php 839 2010-12-21 10:54:20Z deeper $
 */

class Slys_Dojo_View_Helper_TreeSelect extends Zend_Dojo_View_Helper_ComboBox
{
	protected $_module = array('dojo.data.ItemFileWriteStore','dijit.tree.ForestStoreModel');

    public function treeSelect($id, $value = null, array $params = array(), array $attribs = array(), $options = null)
	{
		$this->dojo->requireModule('slys.dijit.CheckBoxTree');

		if(empty($params['store']['type']))
			$params['store']['type'] = 'dojo.data.ItemFileWriteStore';

		$storeParams = array(
			'store' => $params['store']['store'],
			'type' => $params['store']['type'],
			'params' => $params['store']['params']
			);

        $filter = new Zend_Filter_Alnum();
		$store = $this->_renderStore($storeParams, 'store'.$filter->filter($id));
		$forest = array('store'=>new Zend_Json_Expr($storeParams['store']),'childrenAttrs'=>new Zend_Json_Expr('["__children","children"]'));
		$tree = array(
			'jsId' => 'tree'.$filter->filter($id),
			'treeType' => 'slys.dijit.CheckBoxTree',
			'treeParams' => array(
				'treeName' => $id,
				'style' => 'min-height: 100px',
				'class' => 'dijitInlineTable dijitTextBox ',
				'model' => new Zend_Json_Expr('new dijit.tree.ForestStoreModel('.Zend_Json::encode($forest, false, array('enableJsonExprFinder' => true)).')'),
				'showRoot' => false,
				'autoExpand' => true
			)
		);

		if(isset($params['multiple'])){
			if($params['multiple'] === false)
				$tree['treeParams']['type'] = 'radio';
			else
				$tree['treeParams']['type'] = 'checkbox';
		}

		if(!empty($params['treeParams']))
			$tree['treeParams'] = array_merge($tree['treeParams'], $params['treeParams']);
		
		if(!empty($tree['treeParams']['label']))
			$tree['treeParams']['showRoot'] = true;

		if(isset($tree['treeParams']['rootValue']))
			$tree['treeParams']['showRoot'] = true;

		$this->dojo->addJavascript('var ' . $tree['jsId'] . ";\n");
                $js = $tree['jsId'] . ' = '
                    . 'new ' . $tree['treeType'] . '('
                    . Zend_Json::encode($tree['treeParams'], false, array('enableJsonExprFinder' => true))
                    . ", '".$tree['jsId']."');\n";
                $js = "function() {\n$js\n}";
                $this->dojo->_addZendLoad($js);

		$html = '<div id="'.$tree['jsId'].'"></div>';
		return $html;
	}
}