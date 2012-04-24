<?php

/**
 * Slys
 *
 * @author     Evgheni Poleacov <evgheni.poleacov@gmail.com>
 * @uses       Zend_Dojo_Form_Element_Button
 * @package    Slys_Dojo
 * @version    $Id: LinkButton.php 839 2010-12-21 10:54:20Z deeper $
 */

class Slys_Dojo_Form_Element_LinkButton extends Zend_Dojo_Form_Element_Button
{

    /**
     * Use LinkButton dijit view helper
     * @var string
     */
    public $helper = 'LinkButton';

    /**
     * Set url for link button
     *
     * @param string $url
     * @return Slys_Dojo_Form_Element_LinkButton
     */
    public function setUrl($url)
    {
        $this->setDijitParam('url', $url);
        return $this;
    }

    /**
     * Set confirm message for link button
     *
     * @param string $message
     * @return Slys_Dojo_Form_Element_LinkButton
     */
    public function setConfirmMessage($message)
    {
        $this->setDijitParam('confirmMessage', $message);
        return $this;
    }
}