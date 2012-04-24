<?php

/**
 * SlyS
 *
 * This is a class generated with Zend_CodeGenerator.
 *
 * @version $Id: Generator.php 1158 2011-02-02 09:48:32Z deeper $
 * @license New BSD
 */
class Members_Model_Mapper_Member extends Members_Model_Mapper_BaseMember
{
    public function getFullName()
    {
        return $this->firstname . ' ' . $this->lastname;
    }


    public function getFullNameAndNumber()
    {
        return $this->firstname . ' ' . $this->lastname.' ('.$this->number.')';
    }


    /**
     * @return Members_Model_Mapper_Subscription
     */
    public function getActiveSubscription()
    {
        return Members_Model_DbTable_Subscription::getInstance()->createQuery()
            ->select()
            ->where('member_id = ?', $this->id)
            ->andWhere('status = "ACTIVE"')
            ->andWhere('start_date <= ?', date('Y-m-d'))
            ->andWhere('expire_date >= ?', date('Y-m-d'))
            ->orderBy('start_date ASC')->limit(1)->fetchOne();
    }

    /**
     * @return Members_Model_Mapper_Subscription
     */
    public function getLastSubscription() {
         return Members_Model_DbTable_Subscription::getInstance()->createQuery()
                ->select()
                ->where('member_id = ?', $this->id)
                ->andWhereIn('status', array("ACTIVE","PENDING"))
                ->orderBy('expire_date DESC')->limit(1)
                ->fetchOne();
    }

    /**
     * @return Members_Model_Mapper_Subscription
     */
    public function getNextSubscription()
    {
        return Members_Model_DbTable_Subscription::getInstance()->createQuery()
                ->select()
                ->where('member_id = ?', $this->id)
                ->andWhereIn('status', array("ACTIVE","PENDING"))
                ->orderBy('start_date ASC')->limit(1)
                ->fetchOne();
    }

    public function getSubscriptions()
    {
        return Members_Model_DbTable_Subscription::getInstance()->createQuery()
                    ->select()
                    ->where('member_id = ?', $this->id)
                    ->orderBy('start_date DESC')->execute();
    }

    /**
     * @return Members_Model_Mapper_Visit
     */
    public function getLastVisit()
    {
        return Members_Model_DbTable_Visit::getInstance()->createQuery()
                ->select()
                ->where('member_id = ?', $this->id)
                ->orderBy('day DESC, enter_time DESC')->limit(1)->fetchOne();
    }

    public function getCurrentWeekVisits()
    {
        $thisWeekStart = mktime(1, 0, 0, date('m'), date('d')-date('w')+1, date('Y'));

        return Members_Model_DbTable_Visit::getInstance()->createQuery()
                ->select('count(1) as c')
                ->where('day >= ?', date('Y-m-d', $thisWeekStart))
                ->andWhere('member_id = ?', $this->id)
                ->orderBy('day DESC')->fetchOne(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
    }


    public function getLifetimeVisits()
    {
        return Members_Model_DbTable_Visit::getInstance()->createQuery()
                ->select('count(1) as c')
                ->where('member_id = ?', $this->id)
                ->orderBy('day DESC')->fetchOne(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
    }

}

