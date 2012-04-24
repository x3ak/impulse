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
    public function getRecentVisits(Members_Model_Mapper_Member $member, $limit = 5) {
        return $this->createQuery()
                    ->select()
                    ->where('member_id = ?', $member->id)
                    ->orderBy('day DESC, enter_time DESC')->limit($limit)->execute();
    }
}

