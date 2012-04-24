<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('Products_Model_Mapper_BaseSale', 'doctrine');

/**
 * Products_Model_Mapper_BaseSale
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $product_id
 * @property Products_Model_Mapper_Product $Product
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class Products_Model_Mapper_BaseSale extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('products_sales');
        $this->hasColumn('id', 'integer', 11, array(
             'type' => 'integer',
             'unsigned' => true,
             'primary' => true,
             'autoincrement' => true,
             'length' => '11',
             ));
        $this->hasColumn('product_id', 'integer', 11, array(
             'type' => 'integer',
             'unsigned' => true,
             'notnull' => true,
             'length' => '11',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Products_Model_Mapper_Product as Product', array(
             'local' => 'product_id',
             'foreign' => 'id'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}