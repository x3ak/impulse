<?php

/**
 * SlyS
 *
 * This is a class generated with Zend_CodeGenerator.
 *
 * @version $Id: Generator.php 1158 2011-02-02 09:48:32Z deeper $
 * @license New BSD
 */
class Products_Model_DbTable_Product extends Doctrine_Table
{

    /**
     * Returns an instance of this class.
     *
     * @return Products_Model_DbTable_Product
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('Products_Model_Mapper_Product');
    }

    public function getList()
    {
        return $this->createQuery()
            ->select()
            ->where('active = 1')->execute();
    }



    public function getTodaySales() {

        return $this->createQuery('p')
                ->select('COUNT(p.id) as count, SUM(p.price) as total, p.title')
                ->innerJoin('p.Sales s WITH (s.created_at >= ?)', date('Y-m-d') )
                ->groupBy('p.id')
                ->having('total > 0')
                ->execute();
    }

    public function getSevenDaysSales() {
        return $this->createQuery('p')
                ->select('COUNT(p.id) as count, SUM(p.price) as total, p.title')
                ->innerJoin('p.Sales s WITH (s.created_at >= ?)', date('Y-m-d', strtotime('-1 week')) )
                ->groupBy('p.id')
                ->having('total > 0')
                ->execute();
    }
}

