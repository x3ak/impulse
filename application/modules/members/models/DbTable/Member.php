<?php

/**
 * SlyS
 *
 * This is a class generated with Zend_CodeGenerator.
 *
 * @version $Id: Generator.php 1158 2011-02-02 09:48:32Z deeper $
 * @license New BSD
 */
class Members_Model_DbTable_Member extends Doctrine_Table
{

    public static $perPage = 50;
    /**
     * Returns an instance of this class.
     *
     * @return Members_Model_DbTable_Member
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('Members_Model_Mapper_Member');
    }

    /**
     * @param int $page
     * @param string $orderField
     * @param string $orderDirection
     * @return Doctrine_Query
     */
    public function getList($page = 1, $orderField = 'number', $orderDirection = 'desc')
    {
        $dql = $this->createQuery('m')
                ->select()
                ->orderBy($orderField.' '.$orderDirection)
                ->limit(self::$perPage)
                ->offset(self::$perPage * ($page-1) );

        return $dql;
    }


}

