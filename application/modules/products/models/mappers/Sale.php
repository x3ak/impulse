<?php

/**
 * SlyS
 *
 * This is a class generated with Zend_CodeGenerator.
 *
 * @version $Id: Generator.php 1158 2011-02-02 09:48:32Z deeper $
 * @license New BSD
 */
class Products_Model_Mapper_Sale extends Products_Model_Mapper_BaseSale
{
    public function preInsert($event)
    {

        $identity = Zend_Auth::getInstance()->getIdentity();

        $invoker = $event->getInvoker();

        $invoker->user_id = $identity->id;
    }

}

