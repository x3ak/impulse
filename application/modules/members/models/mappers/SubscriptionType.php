<?php

/**
 * SlyS
 *
 * This is a class generated with Zend_CodeGenerator.
 *
 * @version $Id: Generator.php 1158 2011-02-02 09:48:32Z deeper $
 * @license New BSD
 */
class Members_Model_Mapper_SubscriptionType extends Members_Model_Mapper_BaseSubscriptionType
{
    /**
     * @return int
     */
    public function countSubscriptions()
    {
        return count(Members_Model_DbTable_Subscription::getInstance()->findBy('type_id', $this->id, Doctrine_Core::HYDRATE_ARRAY));
    }

}

