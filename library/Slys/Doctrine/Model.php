<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Model
 *
 * @author deep
 */
abstract class Slys_Doctrine_Model
{

    /**
     * Doctrine 2 Entity manager
     *
     * @var Doctrine\ORM\EntityManager
     */
    protected $_em = false;

    /**
     * Return Doctrine 2 entity manager
     * for current connection
     * @return Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        if(empty($this->_em))
            $this->_setEntityManager();
        return $this->_em;
    }

    /**
     * Set entity manager to
     * @return Slys_Doctrine_Model
     */
    private function _setEntityManager()
    {
        $doctrine2 = Zend_Controller_Front::getInstance()
                        ->getParam('bootstrap')
                        ->getResource('doctrine2');

        if(!empty($doctrine2) && $doctrine2 instanceof Slys_Application_Resource_Doctrine2) {
            $this->_em = $doctrine2->getEntityManager();
        }

        return $this;
    }
}
