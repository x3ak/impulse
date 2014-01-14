<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('User_Model_Mapper_BaseUser', 'doctrine');

/**
 * User_Model_Mapper_BaseUser
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $login
 * @property string $password
 * @property enum $role
 * @property string $firstname
 * @property string $lastname
 * @property string $birth
 * @property string $email
 * @property string $phone
 * @property string $zip
 * @property string $token
 * @property datetime $token_date
 * @property Doctrine_Collection $Members
 * @property Doctrine_Collection $Subscriptions
 * @property Doctrine_Collection $Visits
 * @property Doctrine_Collection $Sales
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class User_Model_Mapper_BaseUser extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('user_users');
        $this->hasColumn('id', 'integer', 11, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             'length' => '11',
             ));
        $this->hasColumn('login', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('password', 'string', 40, array(
             'type' => 'string',
             'length' => '40',
             ));
        $this->hasColumn('role', 'enum', null, array(
             'type' => 'enum',
             'values' => 
             array(
              0 => 'MANAGER',
              1 => 'ADMIN',
             ),
             'default' => 'MANAGER',
             ));
        $this->hasColumn('firstname', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('lastname', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('birth', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('email', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('phone', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('zip', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('token', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('token_date', 'datetime', null, array(
             'type' => 'datetime',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('Members_Model_Mapper_Member as Members', array(
             'local' => 'id',
             'foreign' => 'user_id'));

        $this->hasMany('Members_Model_Mapper_Subscription as Subscriptions', array(
             'local' => 'id',
             'foreign' => 'user_id'));

        $this->hasMany('Members_Model_Mapper_Visit as Visits', array(
             'local' => 'id',
             'foreign' => 'user_id'));

        $this->hasMany('Products_Model_Mapper_Sale as Sales', array(
             'local' => 'id',
             'foreign' => 'user_id'));
    }
}