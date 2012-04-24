<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('User_Model_Mapper_BaseRole', 'doctrine');

/**
 * User_Model_Mapper_BaseRole
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $name
 * @property integer $parent_id
 * @property boolean $is_default
 * @property boolean $register
 * @property Doctrine_Collection $Rules
 * @property Doctrine_Collection $User_Model_Mapper_User
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class User_Model_Mapper_BaseRole extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('user_roles');
        $this->hasColumn('id', 'integer', 11, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             'length' => '11',
             ));
        $this->hasColumn('name', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('parent_id', 'integer', 11, array(
             'type' => 'integer',
             'length' => '11',
             ));
        $this->hasColumn('is_default', 'boolean', null, array(
             'type' => 'boolean',
             ));
        $this->hasColumn('register', 'boolean', null, array(
             'type' => 'boolean',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('User_Model_Mapper_Rule as Rules', array(
             'local' => 'id',
             'foreign' => 'role_id'));

        $this->hasMany('User_Model_Mapper_User', array(
             'local' => 'id',
             'foreign' => 'role_id'));
    }
}