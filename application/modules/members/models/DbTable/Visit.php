<?php

/**
 * SlyS
 *
 * This is a class generated with Zend_CodeGenerator.
 *
 * @version $Id: Generator.php 1158 2011-02-02 09:48:32Z deeper $
 * @license New BSD
 */
class Members_Model_DbTable_Visit extends Doctrine_Table
{

    /**
     * Returns an instance of this class.
     *
     * @return Members_Model_DbTable_Visit
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('Members_Model_Mapper_Visit');
    }



    public function getDayVisitsList($day = null)
    {
        if($day === null) {
            $day = date("Y-m-d");
        }

        $dql = $this->createQuery()
                    ->select()
                    ->where('day = ?', $day)
                    ->orderBy('enter_time DESC');

        return $dql->execute();
    }

    /**
     * @param Members_Model_Mapper_Member $member
     * @param int $limit
     * @return Doctrine_Collection
     */
    public function getRecentVisits(Members_Model_Mapper_Member $member, $limit = 7) {
        return $this->createQuery()
                    ->select()
                    ->where('member_id = ?', $member->id)
                    ->orderBy('day DESC, enter_time DESC')->limit($limit)->execute();
    }

    public function getVisitsByWeek($year, $weekNumber) {
        $startOfWeek = new Zend_Date();
        $startOfWeek->setYear($year);
        $startOfWeek->setWeek($weekNumber);
        $startOfWeek->setWeekday(1);
        $startOfWeek->setHour(1);
        $startOfWeek->setMinute(0);
        $startOfWeek->setSecond(0);

        $endOfWeek = new Zend_Date();
        $endOfWeek->setYear($year);
        $endOfWeek->setWeek($weekNumber+1);
        $endOfWeek->setWeekday(1);
        $endOfWeek->setHour(0);
        $endOfWeek->setMinute(0);
        $endOfWeek->setSecond(-1);

        $dql = $this->createQuery('v')
            ->select()
            ->where('v.day >= ?', $startOfWeek->toString('Y-M-d'))
            ->andWhere('v.day < ?', $endOfWeek->toString('Y-M-d'));

        $dql->innerJoin('v.Member m');
        $dql->orderBy('v.enter_time ASC');

        $list = $dql->execute();

        $result = array();
        foreach($list as $visit) {
            $hour = strtok($visit->enter_time, ':');
            $result[$hour][date('N',strtotime($visit->day))][] = $visit;
        }

        return $result;

    }

    /**
     * @return Members_Model_Mapper_Visit
     */
    public function getFirstVisitEver()
    {
        return $this->createQuery()
                ->select()
                ->orderBy('id ASC')
                ->fetchOne();
    }
}

