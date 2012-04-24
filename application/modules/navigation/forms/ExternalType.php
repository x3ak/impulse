<?php
/**
 * Created by JetBrains PhpStorm.
 * User: criollit
 * Date: 06.01.11
 * Time: 14:13
 * To change this template use File | Settings | File Templates.
 */

class Navigation_Form_ExternalType extends Zend_Form_SubForm
{
    public function init()
    {
        $url = new Zend_Form_Element_Text('external_link');
        $url->setLabel('url')
            ->setRequired(true)
            ->setAllowEmpty(false);

        $this->addElement($url);
    }
}