<?php

/**
 * SlyS
 *
 * @author     Evgheni Poleacov <evgheni.poleacov@gmail.com>
 * @version    $Id: Layout.php 1134 2011-01-28 14:31:15Z deeper $
 */
class Templater_Form_Layout extends Zend_Form
{
    /**
     * Form initialization
     */
    public function init()
    {
        $this->loadDefaultDecorators();
        $this->setLegend('New layout');
        $this->addDecorator('fieldset');
        $this->setMethod('POST');
        $element = new Zend_Form_Element_Text('title');
        $element->setLabel('Title:')
                ->setRequired(true);
        $this->addElement($element);

        $themeModel = new Templater_Model_Themes();
        $themes = $themeModel->getThemesPager(1, 10000);

        $themesList = array();
        foreach ($themes->execute() as $theme) {
            $themesList[$theme->id] = $theme->title;
        }

        $element = new Zend_Form_Element_Select('theme_id');
        $element->setLabel('Theme:')
                ->addMultiOptions($themesList)
                ->setRequired(true);
        $this->addElement($element);

        $element = new Zend_Form_Element_Text('name');
        $element->setLabel('Layout file:')
                ->setRequired(true);
        $this->addElement($element);

        $element = new Zend_Form_Element_Checkbox('published');
        $element->setLabel('Published:');
        $this->addElement($element);

        $apiRequest = new Slys_Api_Request($this, 'sysmap.get-map-tree');
        $navigator = $apiRequest->proceed()->getResponse()->getFirst();

        if($navigator instanceof Slys_Form_Element_Tree) {
            $navigator->setName('map_id');
            $navigator->setMultiple(true);
            $this->addElement($navigator);
        }

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
        $element->setLabel('Save')
                ->setIgnore(true);
        $this->addElement($element);
    }

    public function  populate(array $values)
    {
        if(!empty($values['id']))
            $this->setLegend('Edit layout');

        if(!empty($values['Points'])) {
            $points = $values['Points'];
            unset($values['Points']);
            foreach($points as $point)
                $values['map_id'][] = $point['map_id'];
        }

        return parent::populate($values);
    }

}