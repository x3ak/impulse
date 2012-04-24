<?php
/**
 * Created by JetBrains PhpStorm.
 * User: criollit
 * Date: 03.01.11
 * Time: 17:39
 * To change this template use File | Settings | File Templates.
 */

class Sysmap_Form_Extend extends Zend_Form
{
    public function init()
    {
        $title = new Zend_Form_Element_Text('title');
        $title->setLabel('title')
              ->setRequired(true)
              ->setAttrib('maxLength', 100);

        $this->addElement($title);

        /** @var $mapTree Slys_Form_Element_Tree */
        $mapTree = Sysmap_Model_Map::getInstance()->getMapTreeElement();

        $mapTree->addDisableCondition('level', new Zend_Validate_LessThan(3))
                ->addDisableCondition('level', new Zend_Validate_GreaterThan(3));
        $this->addElement($mapTree);

        $submit = new Zend_Form_Element_Submit('submit_extension');
        $submit->setLabel('save')
               ->setOrder(20);
        $this->addElement($submit);


        $id = new Zend_Form_Element_Hidden('id');
        $id->removeDecorator('HtmlTag');
        $id->removeDecorator('Label');
        $this->addElement($id);
    }

    /**
     * Set up values from array
     * @param array $values
     * @return Zend_Form
     */
    public function populate(array $values)
    {
        if (empty($values['sysmap_id']) === false)
            $this->_appendParamsSubform($values['sysmap_id']);

        return parent::populate($values);
    }

    public function isValid($data) {
        if (empty($data['sysmap_id']) === false)
            $this->_appendParamsSubform($data['sysmap_id']);

        return parent::isValid($data);
    }

    protected function _appendParamsSubform($sysmap_id)
    {
        $sysmapItem = Sysmap_Model_DbTable_Sysmap::getInstance()->findOneBy('id', $sysmap_id, Doctrine_Core::HYDRATE_ARRAY);
        $formClass = $sysmapItem['form_name'];

        if (empty($formClass) === false) {
            if (class_exists($formClass) === false)
                throw new Zend_Exception('Associated form class does not exists!');

            $paramsForm = new $formClass();

            if (($paramsForm instanceof Zend_Form_SubForm) === false)
                throw new Zend_Exception('Associated form class must be instance of Zend_Form_SubForm!');

            $this->addSubForm($paramsForm, 'params', $this->getElement('submit_extension')->getOrder() - 1);
        }
    }
}