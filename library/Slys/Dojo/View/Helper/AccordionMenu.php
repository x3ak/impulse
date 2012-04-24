<?php

/**
 * SlyS

 * @author     Evgheni Poleacov <evgheni.poleacov@gmail.com>
 *
 * @version    $Id: AccordionMenu.php 679 2010-12-04 16:09:35Z deeper $
 */

class Slys_Dojo_View_Helper_AccordionMenu extends Zend_Dojo_View_Helper_Dijit
{

	public function accordionMenu($id, Zend_Navigation $navigation, $options = array())
	{
		$content = '';
		$this->view->dojo()->requireModule('dijit.Menu');
		$i = 0;
		$oneSelected = false;
		$active = $this->view->navigation()->menu()->findActive($navigation);
		
		if(empty($active)) {
			$params = Zend_Controller_Front::getInstance()->getRequest()->getParams();
			$options = array('module'=>$params['module'],'controller'=>$params['controller'], 'action'=>$params['action'],'label'=>'');
			$active = array('page'=>new Zend_Navigation_Page_Mvc($options));
		}
        
		foreach ($navigation as $module) {

			if(!empty($active['page']) && $module->module == $active['page']->module) { $oneSelected = true; $selected = true; } else {$selected = false; }
				$menu = '';

				if(!empty($module->pages)) {

					foreach($module->pages as $page) {

						if(!empty($active['page']) && $page == $active['page'])
							$itemSelected = 'selected="selected"';
						else
							$itemSelected = '';

						$i++;
						if(!empty($page->url))
							$url = $page->url;
						else
							$url = $this->view->url(array('module'=>$page->module,'controller'=>$page->controller, 'action'=>$page->action), 'admin', true);
						$menu .= '
							<div dojoType="dijit.Menu" id="accordionMenu'.$id.'-'.$module->module.$i.'">
							<div dojoType="dijit.MenuItem" '.$itemSelected.' iconClass="'.$page->class.'" href="'.$url.'" onClick="window.location.href=\''.$url.'\'">
								'.$page->label.'
							</div>
						</div>
						';
					}
				}

			 $content .= $this->view->accordionPane(
				$module->module.'moduleMenu'.$id.$i,
				$menu,
				array('title' => $module->label, 'selected'=>$selected , 'class'=> $module->class),
				array('style' => 'background-color: white; padding: 2px;')
			);
		 }

		 if(!$oneSelected)
			$this->view->dojo()->addOnLoad("function () {dijit.byId('acontainer{$id}').selectChild();}");

			return $this->view->accordionContainer(
				'acontainer'.$id,
				$content,
				array( 'duration' => 0),
				array( 'style' => 'width: 100%; height: 100%;')
		);

	}
}