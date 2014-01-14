<?php

/**
 * SlyS
 *
 * This is a class generated with Zend_CodeGenerator.
 *
 * @version $Id: Generator.php 1158 2011-02-02 09:48:32Z deeper $
 * @license New BSD
 */
class Members_Model_Mapper_Subscription extends Members_Model_Mapper_BaseSubscription
{
    public function isExpired()
    {
        return $this->status == 'EXPIRED';
    }

    public function isActive()
    {
        return $this->status == 'ACTIVE';
    }

    public function isPending()
    {
        return $this->status == 'PENDING';
    }

    public function activate()
    {
        if($this->status == 'ACTIVE')
            return;

        $this->status = 'ACTIVE';
        $this->start_date = date("Y-m-d");
        $this->expire_date = date("Y-m-d", strtotime("+".$this->Type->duration." ".$this->Type->units));
        $this->save();
    }

    public function expire()
    {
        if($this->status == 'EXPIRED')
            return;

        $this->status = 'EXPIRED';
        $this->save();

        $next = $this->Member->getNextSubscription();

        if(!empty($next))
            $next->activate();
    }

    public function preInsert($event)
    {

        $identity = Zend_Auth::getInstance()->getIdentity();

        $invoker = $event->getInvoker();

        $invoker->user_id = $identity->id;
    }
}

