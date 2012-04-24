<?php
/**
 * Slys
 *
 * Map module bootstrap class
 *
 * @author Serghei Ilin <criolit@gmail.com>
 * @version $Id: Bootstrap.php 1192 2011-02-16 12:43:34Z criolit $
 */
class Sysmap_Bootstrap extends Zend_Application_Module_Bootstrap implements Slys_Api_Request_Requestable, Slys_Api_Notification_Notifier
{
    public function onRequest(Slys_Api_Request $request)
    {
        switch ($request->getName()) {
            case 'sysmap.get-map-tree':
                $request->getResponse()->setData( Sysmap_Model_Map::getInstance()->getMapTreeElement() );
                break;

            case 'sysmap.currently-active-items':
                $params = $request->getParams();

                if (empty($params['request']) === false and $params['request'] instanceof Zend_Controller_Request_Abstract)
                    $request->getResponse()->setData( Sysmap_Model_Map::getInstance()->getActiveItems($params['request']) );
                else
                    $request->getResponse()->setData( Sysmap_Model_Map::getInstance()->getActiveItems() );

                break;

            case 'sysmap.get-item-by-identifier':
                $params = $request->getParams();

                if (empty($params['identifier']) === false and is_string($params['identifier']) === true)
                    $request->getResponse()->setData( Sysmap_Model_Map::getInstance()->getItemByHash($params['identifier']) );

                break;

            case 'sysmap.get-item-parents-by-identifier':
                $params = $request->getParams();

                if (empty($params['identifier']) === false and is_string($params['identifier']) === true)
                    $request->getResponse()->setData( Sysmap_Model_Map::getInstance()->getItemParentsByHash($params['identifier']) );

                break;

            case 'sysmap.get-all-mca':
                $request->getResponse()->setData( Sysmap_Model_Map::getInstance()->getAllMca() );

                break;

            case 'sysmap.create-extend':
                $params = $request->getParams();

                $actionData = $params['action'];
                unset($params['action']);

                $action = Sysmap_Model_DbTable_Sysmap::getInstance()->findAction($actionData['module'], $actionData['controller'],$actionData['action'])->toArray();

                $params['sysmap_id'] = $action['id'];

                $extend = Sysmap_Model_Map::getInstance()->addExtend($params);

                break;
        }
    }

    /**
     * @param Slys_Api_Notification_Registry $registry
     * @return void
     */
    public function publishNotifications(Slys_Api_Notification_Registry $registry)
    {
        $registry->register('sysmap.item-deleted');
        $registry->register('sysmap.item-updated');
    }
}