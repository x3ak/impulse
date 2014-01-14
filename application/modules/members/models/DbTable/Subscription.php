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

    public function getTodaySales() {
        return $this->createQuery('s')
                ->select()
                ->innerJoin('s.Type')
                ->where('s.created_at >= DATE_SUB(NOW(),INTERVAL 1 DAY)')
                ->execute();
    }

    public function getSevenDaysSales() {
        return $this->createQuery('s')
                ->select()
                ->innerJoin('s.Type')
                ->where('s.created_at >= DATE_SUB(NOW(),INTERVAL 7 DAY)')
                ->execute();
    }

    public function getWeekSales($start, $end, $type = null) {
        $startOfWeek = new Zend_Date($start);
        $startOfWeek->setHour(1);
        $startOfWeek->setMinute(0);
        $startOfWeek->setSecond(0);

        $endOfWeek = new Zend_Date($end);
        $endOfWeek->setHour(0);
        $endOfWeek->setMinute(0);
        $endOfWeek->setSecond(0);
        $endOfWeek->addDay(1);

        $dql = $this->createQuery('s')->select();

        if(!is_null($type)) {
            $dql->innerJoin('s.Type t WITH t.id = ?', $type);
        } else {
            $dql->innerJoin('s.Type t');
        }

        $dql->innerJoin('s.Member')
        ->where('s.created_at >= ?', $startOfWeek->toString('Y-M-d'))
        ->andWhere('s.created_at <= ?', $endOfWeek->toString('Y-M-d'));
        return $dql->execute();


    }


}

