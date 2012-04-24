<?php

/**
 * SlyS
 *
 * This is a class generated with Zend_CodeGenerator.
 *
 * @version $Id: Generator.php 1158 2011-02-02 09:48:32Z deeper $
 * @license New BSD
 */
class Members_Model_DbTable_Subscription extends Doctrine_Table
{

    /**
     * Returns an instance of this class.
     *
     * @return Members_Model_DbTable_Subscription
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('Members_Model_Mapper_Subscription');
    }

    public function getActiveThatExpired()
    {
        return $this->createQuery()
                ->select()
                ->where('expire_date < ?', date('Y-m-d'))
                ->andWhere('status = ?', 'ACTIVE')
                ->execute();
    }

}

