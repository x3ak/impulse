<?php

/**
 * Slys
 *
 * @author     Evgheni Poleacov <evgheni.poleacov@gmail.com>
 * @version    $Id: Table.php 839 2010-12-21 10:54:20Z deeper $
 */
class Slys_Dojo_View_Helper_Table extends Zend_Dojo_View_Helper_ComboBox
{

    /**
     * Table id
     * @var string
     */
    protected $_id;

    /**
     * Table structure
     * @var array
     */
    protected $_structure = array();

    /**
     * Zend_Dojo_Data
     * @var array
     */
    protected $_data;

    /**
     * Store params
     * @var array
     */
    protected $_params = array();

    /**
     * Store data url
     * @var string
     */
    protected $_url;

    /**
     * Set table id
     * @param int $id 
     */
    public function setId($id)
    {
        $this->_id = $id;
        return $this;
    }
    
    /**
     * Set structure
     *
     * @param array $structure
     * @return Slys_Dojo_View_Helper_Table
     */
    public function setStructure($structure)
    {
        $this->_structure = $structure;
        return $this;
    }

    /**
     * Set tabe data
     * @param Zend_Dojo_Data $dojoData
     * @return Slys_Dojo_View_Helper_Table 
     */
    public function setData(Zend_Dojo_Data $dojoData = null)
    {
        $this->_data = $dojoData;
        return $this;
    }

    /**
     * Set remote data URL
     * @param string $url
     * @return Slys_Dojo_View_Helper_Table
     */
    public function setUrl($url)
    {
        $this->_url = $url;
        return $this;
    }

    public function setParams($params)
    {
        $this->_params = $params;
        return $this;
    }

    /**
     * Return HTML for simple table and prepare Dojo Grid table programatically
     *
     * @param array $params
     * @return string
     */
    public function render($params = array())
    {
        $this->view->dojo()->requireModule('dojo.data.ItemFileWriteStore');
        $this->view->dojo()->requireModule('dojox.grid.EnhancedGrid');
        $this->view->dojo()->requireModule('dojox.grid.enhanced.plugins.Pagination');
        $this->view->dojo()->addStylesheet('/js/dojox/grid/enhanced/resources/claro/EnhancedGrid.css');

        if(empty($this->_url)) {
            $firstColumn = current($this->_structure);

            $firstColumnName = $firstColumn['field'];
            if(empty($this->_data) && !$this->_data instanceof Zend_Dojo_Data)
                throw new Zend_Exception('Data as Zend_Dojo_Data not provided');

            $data = $this->_prepareTreeGrid($this->_data->getItems(), $firstColumnName, $this->_data->getIdentifier());
            $this->_data->setItems($data);

            
            $html = $this->_getDijitsHtmlTable($this->_data->toArray());

            $storeParams = array('data' => $this->_data->toArray());
        } else {
            $html = '';
            $storeParams = array('url' => $this->_url);
        }

        $this->_prepareDijits();

        $store = new Zend_Json_Expr('new dojo.data.ItemFileWriteStore('
            . Zend_Json::encode($storeParams, false, array('enableJsonExprFinder' => true)) . ')');

        
        $gridParams = array('structure' => $this->_structure, 'autoHeight' => true);
        $gridParams['store'] = $store;
        $gridParams['plugins']['pagination'] = true;
        $gridParams['escapeHTMLInData'] = false;

        $gridParams = array_merge($gridParams, $this->_params);
        $gridParams = array_merge($gridParams, $params);
        $this->view->dojo()->onLoadCaptureStart();
?>
            function () {
                grid = dojox.grid.EnhancedGrid(<?php echo Zend_Json::encode(
                    $gridParams, false, array('enableJsonExprFinder' => true));
                ?>, dojo.byId('<?php echo $this->_id; ?>'));
                grid.startup();
            }
<?php
        $this->view->dojo()->onLoadCaptureEnd();

        // TODO: Create simple HTML form by provided structure and incoming data. Make support of decorators for every cell.
        return '<div class="dojoTable"><table id="' . $this->_id . '">'
        . $html . '</table><div id="pagination" class="pagination"></div></div>';
    }

    /**
     * Return current class
     * @return Slys_Dojo_View_Helper_Table
     */
    public function table()
    {
        return $this;
    }

    /**
     * Render table
     * @return string
     */
    public function  __toString()
    {
        return $this->render($this->_params);
    }


    /**
     * Prepare dijits provided for using in table cells
     */
    protected function _prepareDijits()
    {
        foreach ($this->_structure as $key => $field) {
            if (!empty($field['dijits']) && is_array($field['dijits'])) {

                $dijits = array('id' => 'cell-id-' . uniqid(), 'field' => $field['field']);
                foreach ($field['dijits'] as $dijitkey => $dijit) {

                    if ($dijit instanceof Zend_Dojo_Form_Element_Dijit) {
                        $content = $dijit->render($this->view);
                        $content = addslashes(str_replace("\n", '', $content));
                        $this->_structure[$key]['dijits'][$dijitkey] = $content;

                        if ($this->view->dojo()->hasDijit($dijit->getName())) {
                            $dijitParams = $this->view->dojo()->getDijit($dijit->getName());
                            if (!empty($dijitParams)) {
                                $this->view->dojo()->requireModule($dijitParams['dojoType']);
                                $dijitParams['id'] = 'dijit-' . uniqid();
                                $dijitParams['name'] = $dijit->getName() . '[]';
                                if ($dijit instanceof Zend_Dojo_Form_Element_DijitMulti)
                                    $dijitParams['name'] = $dijit->getName();
                                $dijits['dijits'][] = $dijitParams;
                            }
                            $this->view->dojo()->removeDijit($dijit->getName());
                        }
                    }
                }

                /*
                 * Prepare all founded dijits into cell "get" function like elements of dijit.MenuBar
                 */

                if (!empty($dijits)) {
                    $this->view->dojo()->requireModule('dijit.Toolbar');
                    $this->view->dojo()->javascriptCaptureStart();
?>
                    function setDijitCellValue(key, item, dijits)
                    {

                    var identity = this.grid.store.getIdentity(item);

                    var value = this.grid.store.getValue(item, dijits.field);

                    var menuid = dijits.id+'-'+identity;
                    var actionmenu;
                    if(dijit.byId(menuid))
                    actionmenu = dijit.byId(menuid);
                    else
                    actionmenu = new dijit.Toolbar({id:menuid});
                    dojo.forEach(dijits.dijits, function(item, i) {
                    var dijitId = item.id+'-'+identity;
                    var dijitObject;
                    item.id = dijitId;
                    item.value = identity;

                    item.checked = value;
                    var callback = 'empty';
                    if(item.onClick != undefined) {
                    item.onClick = dojo.replace(item.onClick, { identity:identity, value:value, key:key });
                    eval('callback = function() { '+item.onClick+' }');
                    delete item.onClick;
                    }

                    if(dijit.byId(dijitId))
                    dijitObject = dijit.byId(dijitId);
                    else
                    dijitObject = dojo.eval('new '+item.dojoType+'('+dojo.toJson(item)+');');

                    if(callback != 'empty') { dojo.connect(dijitObject, "onClick", null, callback);}


                    actionmenu.addChild(dijitObject);
                    });
                    return actionmenu;
                    }
<?php
                    $this->view->dojo()->javascriptCaptureEnd();
                    $this->_structure[$key]['get'] = new Zend_Json_Expr(
                        ' function (key, item) { return setDijitCellValue(key, item, ' .
                        Zend_Json::encode($dijits,
                            false, array('enableJsonExprFinder' => true)) . '); } ');
                }
            }
        }
    }

    /**
     * Return HTML of simple table
     *
     * @param array $data
     * @return string
     */
    protected function _getDijitsHtmlTable($data)
    {
        foreach ($this->_structure as $key => $element) {
            unset($this->_structure[$key]['dijits']);
        }
        return '';
    }

    /**
     * Convert tree hierachical data into array
     * 
     * @param array $data
     * @param sting $column
     * @param string $id
     * @return array
     */
    public function _prepareTreeGrid($data, $column, $id)
    {
        foreach($data as $key=>$item) {
            if(is_array($item))
                foreach($item as $fkey=>$field) {
                    if(!is_array($field)) {
                       if($fkey == $column && !empty($item['level'])) {
                           $separator = '<span class="tree-level-close"></span>';
                            if($item['level'] > 1)
                                $separator = str_repeat ('<span class="tree-level"></span>', $item['level']-1).$separator;
                            $field = $separator.$field;
                       }
                            $this->_tempdata[$item[$id]][$fkey] = $field;
                    } elseif($fkey == '__children') {
                       $this->_prepareTreeGrid($item, $column, $id);
                    }
                }
        }        
        return $this->_tempdata;
    }
}