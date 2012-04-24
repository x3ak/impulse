<?php

/**
 * Slys
 *
 * Template layout switcher. Used to check and switch layout for the current theme
 * such file exists
 *
 * @author Serghei Ilin <criolit@gmail.com>
 */
class Members_Plugin_Expire extends Zend_Controller_Plugin_Abstract
{
    public function routeShutdown(Zend_Controller_Request_Abstract $request)
    {
        $expired = Members_Model_DbTable_Subscription::getInstance()->getActiveThatExpired();

        /** @var $subscription Members_Model_Mapper_Subscription */
        foreach($expired as $subscription) {
            $subscription->expire();
        }

        parent::routeShutdown($request);
    }

}