<?php
/**
 * Created by JetBrains PhpStorm.
 * User: criollit
 * Date: 17.01.11
 * Time: 18:01
 * To change this template use File | Settings | File Templates.
 */

interface Slys_Api_Notification_Notifier
{
    /**
     * @abstract
     * @param Slys_Api_Notification_Registry $registry
     * @return void
     */
    public function publishNotifications( Slys_Api_Notification_Registry $registry );
}