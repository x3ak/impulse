<?php

/**
 * SlyS
 *
 * @author     Evgheni Poleacov <evgheni.poleacov@gmail.com>
 *
 * @version    $Id: Widget.php 1065 2011-01-20 10:04:22Z deeper $
 */
class Templater_Form_Widget extends Zend_Dojo_Form
{
    /**
     * Form initialization
     */
    public function init()
    {
        $this->setMethod('POST');

        $themeModel = new Templater_Model_Themes();
        $themes = $themeModel->getThemesPager(1, 10000);

        $themesList = array();
        foreach ($themes->execute() as $theme) {
            $layoutsList = array();

            foreach ($theme->Layouts as $layout)
                $layoutsList[$layout->id] = $layout->title;

            $themesList[$theme->title] = $layoutsList;
        }

        $element = new Zend_Form_Element_Select('layout_id');
        $element->setLabel('Theme layout:')
                ->addMultiOptions($themesList)
                ->addDecorator('fieldset')
                ->setRequired(true);
        $this->addElement($element);

        $element = new Zend_Form_Element_Text('name');
        $element->setLabel('Widget name:')
                ->setRequired(true);
        $this->addElement($element);


        $element = new Zend_Form_Element_Text('placeholder');
        $element->setLabel('Position:')
                ->setRequired(true);
        $this->addElement($element);

        $this->addDisplayGroup(array('placeholder','name'), 'place', array('order' => 2, 'legend' => 'Display options:'));

        $apiRequest = new Slys_Api_Request($this, 'sysmap.get-map-tree');
        $sysmapElement = $apiRequest->proceed()->getResponse()->getFirst();

        $actionNavigator = clone $sysmapElement;
        
        if ($actionNavigator instanceof Slys_Form_Element_Tree) {
            $actionNavigator->setName('map_id');
            $actionNavigator->setLabel('Widget content provider action:');
            $actionNavigator->addDisableCondition('level', new Zend_Validate_LessThan(3));
            $this->addElement($actionNavigator);
        }

        $displayNavigator = clone $sysmapElement;
        
        if ($displayNavigator instanceof Slys_Form_Element_Tree) {
            $displayNavigator->setName('widget_points');
            $displayNavigator->setLabel('Widget display pages:');
            $displayNavigator->setMultiple(true);
            $this->addElement($displayNavigator);
        }
        
        $element = new Zend_Form_Element_Text('ordering');
        $element->addValidator(new Zend_Validate_Int())
                ->setRequired(true)
                ->setLabel('Ordering:')
                ->setAttrib('style', 'width:50px;');
        $this->addElement($element);

        $element = new Zend_Form_Element_Checkbox('published');
        $element->setLabel('Published:');
        $this->addElement($element);

        $this->addDisplayGroup(array('ordering', 'published'), 'other',
                array('legend' => 'Publishing options:'));


        $element = new Zend_Form_Element_Hidden('module');
        $element->removeDecorator('Label')
                ->removeDecorator('HtmlTag')
                ->setValue($this->_defaultValue);
        $this->addElement($element);

        $element = new Zend_Form_Element_Hidden('controller');
        $element->removeDecorator('Label')
                ->setValue($this->_defaultValue)
                ->removeDecorator('HtmlTag');
        $this->addElement($element);

        $element = new Zend_Form_Element_Hidden('action');
        $element->removeDecorator('Label')
                ->setValue($this->_defaultValue)
                ->removeDecorator('HtmlTag');
        $this->addElement($element);

        $element = new Zend_Form_Element_Submit('submit');
        $element->setLabel('Save');

        $element->setIgnore(true);
        $this->addElement($element);

        foreach ($this->getDisplayGroups() as $group) {
            $group->setDecorators(array('description', 'FormElements', 'fieldset'));
        }

        foreach ($this->getSubForms() as $group) {
            $group->setDecorators(array('description', 'FormElements', 'fieldset'));
        }
    }

    public function populate(array $values)
    {
        if (!empty($values['id']))
            $this->setLegend('Edit Widget');

        if(!empty($values['WidgetPoints'])) {
            $points = $values['WidgetPoints'];
            foreach($points as $point)
                $values['widget_points'][] = $point['map_id'];
        }

        return parent::populate($values);
    }

}