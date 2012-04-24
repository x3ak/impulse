<?php
/**
 * Description
 *
 * @author Serghei Ilin <criolit@gmail.com>
 * @version $Id$
 */

class Sysmap_Form_ExtendPattern extends Zend_Form
{
    public function init()
    {
        $this->setMethod(self::METHOD_POST);

        $title = new Zend_Form_Element_Text('title');
        $title->setLabel( $this->getView()->translate('title') );
        $this->addElement($title);

        $modules = new Zend_Form_Element_Select('mca_module');
        $modules->setLabel( $this->getView()->translate('module') )
                ->setDisableTranslator(true)
                ->addMultiOptions(array('*' => '*'));

        $this->addElement($modules);

        $controllers = new Zend_Form_Element_Select('mca_controller');
        $controllers->setLabel( $this->getView()->translate('controller') )
                    ->setDisableTranslator(true)
                    ->addMultiOptions(array('*' => '*'));

        $this->addElement($controllers);

        $actions = new Zend_Form_Element_Select('mca_action');
        $actions->setLabel( $this->getView()->translate('action') )
                ->setDisableTranslator(true)
                ->addMultiOptions(array('*' => '*'));

        $this->addElement($actions);

        $submit = new Zend_Form_Element_Submit('submit_pattern');
        $submit->setLabel('save');
        $this->addElement($submit);

        $id = new Zend_Form_Element_Hidden('id');
        $id->removeDecorator('HtmlTag');
        $id->removeDecorator('Label');
        $this->addElement($id);

        $this->_fillMcaForms();
    }

    protected function _fillMcaForms()
    {
        $distinctMca = Sysmap_Model_Map::getInstance()->getAllMca();

        $this->getElement('mca_module')->addMultiOptions($distinctMca['modules']);
        $this->getElement('mca_controller')->addMultiOptions($distinctMca['controllers']);
        $this->getElement('mca_action')->addMultiOptions($distinctMca['actions']);
    }
}