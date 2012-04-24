<?php
/**
 * Created by JetBrains PhpStorm.
 * User: criollit
 * Date: 06.01.11
 * Time: 14:16
 * To change this template use File | Settings | File Templates.
 */

class Navigation_Form_ProgrammaticType extends Zend_Form_SubForm
{
    public function init()
    {
        /**
         * @var $map Slys_Form_Element_Tree
         */
        $mapTree = Slys_Api::getInstance()->request(new Slys_Api_Request($this, 'sysmap.get-map-tree'))->getFirst();
        $mapTree->setName('sysmap_identifier')
                ->addDisableCondition('level', new Zend_Validate_LessThan(3));
        $this->addElement($mapTree);
    }
}