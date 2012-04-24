<?php

/**
 * SlyS
 * 
 * This is a class generated with Zend_CodeGenerator.
 * 
 * @version $Id: Role.php 1254 2011-05-04 11:47:12Z deeper $
 * @license New BSD
 */

/**
 * @Entity
 * @Table(name="user_roles2")
 */
class User_Model_Mapper_Role extends User_Model_Mapper_BaseRole
{
    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     */
    protected $id;

    /** @Column(length=50) */
    protected $name;

    /** @Column(type="integer") */
    protected $parent_id;

    /** @Column(type="boolean") */
    protected $is_default;

}

