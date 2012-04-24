<?php

/**
 * Slys
 *
 * @version    $Id: Widgets.php 1056 2011-01-19 14:38:17Z deeper $
 */
class Templater_Model_Widgets
{

    /**
     * Return list of all widgets
     *
     * @return Doctrine_Collection
     */
    public function getlist()
    {
        return Templater_Model_DbTable_Widget::getInstance()
            ->getWidgets();
    }

    /**
     * Return widget entity
     *
     * @param int $id
     * @param boolean $forEdit
     * @return Templater_Model_Mapper_Widget
     */
    public function getWidget($id, $forEdit = false)
    {
        if (empty($id) && $forEdit)
            $widget = new Templater_Model_Mapper_Widget();
        else
            $widget = Templater_Model_DbTable_Widget::getInstance()
                    ->getWidgetWithWidgetPoints($id);

        if (empty($widget) && $forEdit)
            $widget = new Templater_Model_Mapper_Widget();

        return $widget;
    }

    /**
     * Save widget type
     * @param array $values
     * @return boolean
     */
    public function saveWidget(Templater_Model_Mapper_Widget $widget, $values)
    {
        
        Templater_Model_DbTable_WidgetPoint::getInstance()
            ->deleteUnusedPoints($widget->id, $values['widget_points']);

        $widget->fromArray($values);

        if(!empty($values['widget_points'])) {
            foreach($values['widget_points'] as $key=>$mapId) {
                $point = Templater_Model_DbTable_WidgetPoint::getInstance()->findByDql("map_id = ? AND widget_id = ?", array($mapId, $widget->id));
                
                if($point->count() < 1) {
                    $point = new Templater_Model_Mapper_WidgetPoint();
                    $point->set('map_id', $mapId);
                    $widget->WidgetPoints->add($point);
                }
            }
        }

        return $widget->save();
    }

    /**
     * Return pager of widgets
     *
     * @param int $page
     * @param int $maxPerPage
     * @return Doctrine_Pager
     */
    public function getWidgetsPager($page = 1, $maxPerPage = 20)
    {
        return Templater_Model_DbTable_Widget::getInstance()
            ->getPager($page, $maxPerPage);
    }

    /**
     * Return widget params form
     *
     * @param string $module
     * @param string $controller
     * @param string $action
     * @return Zend_Form|boolean
     */
    public function getWidgetParamForm($providerName)
    {
        if (class_exists($providerName)) {
            $widget = new $providerName();
            return $widget->getParamsForm();
        }
        else
            return false;
    }

    /**
     * Return description of widget action
     *
     * @param string $module
     * @param string $controller
     * @param string $action
     * @return string
     */
    public function _getWidgetName($providerName)
    {
        return Slys_Esb::getInstance()
            ->getProvider($providerName)
            ->getName();
    }

    /**
     * Return array of actions which support widgets
     *
     * @param string $module
     * @return array
     */
    public function _getActiveWidgets($module = null)
    {
        $providers = Slys_Esb::getInstance()
                ->getProviders('Templater_Esb_Api_Widget');

        $controllers = array();
        $providers = $providers->getByName($module);
        foreach ($providers as $provider) {
            $controllers[] = array(
                'name' => $provider->getName(),
                'description' => $provider->getName() .
                '<div class="select-option-description">' .
                $provider->getDescription() . '</div>',
                'value' => $provider->getClass());
        }
        return $controllers;
    }

     /**
     * Delete Widget
     * @param int $id
     * @return boolean
     */
    public function deleteWidget($id)
    {
        $widget = new Templater_Model_Mapper_Widget();
        $widget->assignIdentifier($id);
        return $widget->delete();
    }

}