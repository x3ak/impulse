<?php

/**
 * SlyS
 *
 * This is a class generated with Zend_CodeGenerator.
 *
 * @version $Id: Generator.php 1158 2011-02-02 09:48:32Z deeper $
 * @license New BSD
 */
class Members_Model_DbTable_SubscriptionType extends Doctrine_Table
{

    /**
     * Returns an instance of this class.
     *
     * @return Members_Model_DbTable_SubscriptionType
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('Members_Model_Mapper_SubscriptionType');
    }

    public function getTodaySales() {
        return $this->createQuery('t')
                ->select('COUNT(s.id) as count, SUM(s.price_on_signup) as total, t.title')
                ->innerJoin('t.Subscriptions s WITH (s.created_at >= ?)', date('Y-m-d') )
                ->groupBy('t.id')
                ->having('total > 0')
                ->execute();
    }

    public function getSevenDaysSales() {
        return $this->createQuery('t')
                ->select('COUNT(s.id) as count, SUM(s.price_on_signup) as total, t.title')
                ->innerJoin('t.Subscriptions s WITH (s.created_at >= ?)', date('Y-m-d', strtotime('-1 week')) )
                ->groupBy('t.id')
                ->having('total > 0')
                ->execute();

    }

}

