<?php

/**
 * SlyS
 *
 * @author     Evgheni Poleacov <evgheni.poleacov@gmail.com>
 *
 * @version    $Id: Role.php 1190 2011-02-10 17:04:55Z deeper $
 */
class User_Form_Role extends Zend_Form
{

    /**
     * Form inititalization
     */
    public function init()
    {

        $this->setMethod('POST');

        $element = new Zend_Form_Element_Text('name');
        $element->setLabel('Role name:');
        $element->setRequired(true);
        $this->addElement($element);

        $rolesModel = new User_Model_Roles();
        $roles = $rolesModel->getlist();
        $parents = array();
        foreach ($roles as $role) {
            $parents[$role->id] = $role->name;
        }

        $apiRequest = new Slys_Api_Request($this, 'sysmap.get-map-tree');
        $actionNavigator = $apiRequest->proceed()->getResponse()->getFirst();

        if ($actionNavigator instanceof Slys_Form_Element_Tree) {
            $actionNavigator->setName('resources');
            $actionNavigator->setMultiple(true);
            $actionNavigator->setRequired(false);
            $actionNavigator->setLabel('ACL allowed resources:');
            $this->addElement($actionNavigator);
        }

        $element = new Zend_Form_Element_Select('parent_id');
        $element->setLabel('Parent role:');
        $element->addMultiOption('','No parents');
        $element->addMultiOptions($parents);
        $this->addElement($element);

        $submitElement = new Zend_Form_Element_Submit('submit');
        $submitElement->setIgnore(true);
        $this->addElement($submitElement);
    }

    public function populate(array $values)
    {
        if (!empty($values['id']))
            $this->setLegend('Edit Role');

        if(!empty($values['Rules'])) {
            $points = $values['Rules'];
            foreach($points as $point)
                $values['resources'][] = $point['resource_id'];
        }

        return parent::populate($values);
    }

}