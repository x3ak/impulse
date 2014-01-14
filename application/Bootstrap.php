<?php
/**
 * Slys
 *
 * Default application bootstrap class
 *
 * @author Serghei Ilin <criolit@gmail.com>
 * @version $Id: Bootstrap.php 1077 2011-01-20 13:50:19Z criolit $
 */

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initAcl() {
        //allowed m.c.a
        $acl = array('MANAGER'=>array(
            'default.index.index',
            'admin.index.index',
            'error.index.index',
            'user.auth.login',
            'user.auth.logout',
            'members.admin.index',
            'members.admin.list',
            'members.admin.add',
            'members.admin.view',
            'members.admin.add-subscription',
            'members.subscription.view',
            'members.visit.new',
            'members.visit.view',
            'members.visit.finish',
            'members.visit.fast',
            'products.admin.buy',
        ));
        Zend_Registry::set('ACL', $acl);
    }
}