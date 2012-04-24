<?php
/**
 * Created by JetBrains PhpStorm.
 * User: criollit
 * Date: 17.01.11
 * Time: 17:57
 * To change this template use File | Settings | File Templates.
 */

interface Slys_Api_Request_Requestable
{
    public function onRequest(Slys_Api_Request $request);
}