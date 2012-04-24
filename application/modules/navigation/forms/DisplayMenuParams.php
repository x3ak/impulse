<?php
/**
 * Description
 *
 * @author Serghei Ilin <criolit@gmail.com>
 * @version $Id$
 */

class Navigation_Form_DisplayMenuParams extends Zend_Form_SubForm
{
    public function init()
    {
        $this->setMethod(self::METHOD_POST);

        $navigator = new Slys_Form_Element_Tree('item_id');
        $navigator->setMultiple(true)
                  ->setRequired(true)
                  ->setValueKey('id')
                  ->setTitleKey('label')
                  ->setChildrensKey('pages');

        $navigator->setMultiOptions( Navigation_Model_Navigation::getInstance()->getNavigation()->toArray() );

        $this->addElement($navigator);

        $cssStyle = new Zend_Form_Element_Text('css');
        $cssStyle->setLabel('css styles');
        $this->addElement($cssStyle);

        $partialName = new Zend_Form_Element_Text('partial');
        $partialName->setLabel('partial name');
        $this->addElement($partialName);
    }
}