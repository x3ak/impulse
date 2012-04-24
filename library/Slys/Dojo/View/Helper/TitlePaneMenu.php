<?php

/**
 * SlyS

 * @author     Evgheni Poleacov <evgheni.poleacov@gmail.com>
 *
 * @version    $Id: TitlePaneMenu.php 839 2010-12-21 10:54:20Z deeper $
 */

class Slys_Dojo_View_Helper_TitlePaneMenu extends Zend_Dojo_View_Helper_Dijit
{

	public function titlePaneMenu($id, Zend_Navigation $navigation, $options = array())
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
				$menu = '<ul dojoType="dijit.Menu">';

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
						$menu .= '<a href="'.$url.'" dojoType="dijit.MenuItem" '.$itemSelected.
                            ' class="menu-item" iconClass="'.$page->class.'" onClick="window.location.href=\''.$url.
                            '\'">'.$page->label.'</a>';
					}
				}
             $menu .= '</ul>';
			 $content .= $this->view->titlePane(
				$module->module.'moduleMenu'.$id.$i,
				$menu,
				array('title' => $module->label, 'open'=>$selected , 'class'=> 'titlePaneMenu '.$module->class)
			);
		 }

		 return $content;

	}
}