<?php

/**
 * SlyS
 *
 * This is a class generated with Zend_CodeGenerator.
 *
 * @version $Id: Generator.php 1158 2011-02-02 09:48:32Z deeper $
 * @license New BSD
 */
class Products_Model_Mapper_Product extends Products_Model_Mapper_BaseProduct
{

    public function buy()
    {
        if($this->amount == 0)
            return;


        $sale = new Products_Model_Mapper_Sale();
        $sale->product_id = $this->id;
        $sale->save();


        $this->amount--;
        $this->save();

    }

    public function getTodaysSales() {
        return Products_Model_DbTable_Sale::getInstance()->createQuery()
            ->select()
            ->where('product_id = ?', $this->id)
            ->andWhere('created_at >= DATE_SUB(NOW(),INTERVAL 1 DAY)')
            ->execute()
        ;
    }


    public function getLastWeekSales() {
        return Products_Model_DbTable_Sale::getInstance()->createQuery()
                ->select()
                ->where('product_id = ?', $this->id)
                ->andWhere('created_at >= DATE_SUB(NOW(),INTERVAL 7 DAY)')
                ->execute();
    }
}

