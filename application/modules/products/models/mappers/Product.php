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
        $sale = new Products_Model_Mapper_Sale();
        $sale->product_id = $this->id;
        $sale->save();

    }
}

