<?php

class User_Plugin_Navigation extends Zend_Controller_Plugin_Abstract
{

    public function routeShutdown(Zend_Controller_Request_Abstract $request)
    {
        $view = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('view');
        $navigation = new Zend_Navigation();

        $identity = Zend_Auth::getInstance()->getIdentity();

        if(empty($identity)) return true;

        $members = new Zend_Navigation_Page_Mvc();
        $members->setLabel('Members');
        $members->setModule('members');
        $members->setController('admin');
        $members->setRoute('default');
        $members->setResetParams(true);
        $navigation->addPage($members);

        $page = new Zend_Navigation_Page_Mvc();
        $page->setLabel('List');
        $page->setModule('members');
        $page->setController('admin');
        $page->setRoute('default');
        $page->setAction('list');
        $page->setResetParams(true);
        $members->addPage($page);

        $page = new Zend_Navigation_Page_Mvc();
        $page->setLabel('Report');
        $page->setModule('members');
        $page->setController('report');
        $page->setRoute('default');
        $page->setAction('emails-list');
        $page->setResetParams(true);
        $members->addPage($page);

        $page = new Zend_Navigation_Page_Mvc();
        $page->setLabel('Report');
        $page->setModule('members');
        $page->setController('report');
        $page->setRoute('default');
        $page->setAction('week-visits-report');
        $page->setResetParams(true);
        $members->addPage($page);


        $page = new Zend_Navigation_Page_Mvc();
        $page->setLabel('Edit');
        $page->setModule('members');
        $page->setController('admin');
        $page->setRoute('default');
        $page->setAction('edit');
        $page->setResetParams(true);
        $members->addPage($page);

        $page = new Zend_Navigation_Page_Mvc();
        $page->setLabel('Add');
        $page->setModule('members');
        $page->setController('admin');
        $page->setRoute('default');
        $page->setAction('add');
        $page->setResetParams(true);
        $members->addPage($page);

        $page = new Zend_Navigation_Page_Mvc();
        $page->setLabel('View');
        $page->setModule('members');
        $page->setController('admin');
        $page->setRoute('default');
        $page->setAction('view');
        $page->setResetParams(true);
        $members->addPage($page);


        $productsBuy = new Zend_Navigation_Page_Mvc();
        $productsBuy->setLabel('Products Dashboard');
        $productsBuy->setModule('products');
        $productsBuy->setController('admin');
        $productsBuy->setAction('buy');
        $productsBuy->setRoute('default');
        $productsBuy->setResetParams(true);
        $navigation->addPage($productsBuy);

        if($identity->role == 'ADMIN') {
            $products = new Zend_Navigation_Page_Mvc();
            $products->setLabel('Products List');
            $products->setModule('products');
            $products->setController('admin');
            $products->setRoute('default');
            $products->setResetParams(true);
            $navigation->addPage($products);

            $productsEdit = new Zend_Navigation_Page_Mvc();
            $productsEdit->setLabel('Products List');
            $productsEdit->setModule('products');
            $productsEdit->setController('admin');
            $productsEdit->setAction('edit');
            $productsEdit->setRoute('default');
            $productsEdit->setResetParams(true);
            $products->addPage($productsEdit);


            $subscrTypes = new Zend_Navigation_Page_Mvc();
            $subscrTypes->setLabel('Subscription Types');
            $subscrTypes->setModule('members');
            $subscrTypes->setController('subscription');
            $subscrTypes->setAction('list');
            $subscrTypes->setRoute('default');
            $subscrTypes->setResetParams(true);
            $navigation->addPage($subscrTypes);

            $subscrEdit = new Zend_Navigation_Page_Mvc();
            $subscrEdit->setLabel('Subscription edit');
            $subscrEdit->setModule('members');
            $subscrEdit->setController('subscription');
            $subscrEdit->setAction('edit');
            $subscrEdit->setRoute('default');
            $subscrEdit->setResetParams(true);
            $subscrTypes->addPage($subscrEdit);

            $users = new Zend_Navigation_Page_Mvc();
            $users->setLabel('Users');
            $users->setModule('user');
            $users->setAction('users');
            $users->setController('admin');
            $users->setRoute('default');
            $users->setResetParams(true);
            $navigation->addPage($users);

            $editUser = new Zend_Navigation_Page_Mvc();
            $editUser->setLabel('Users Edit');
            $editUser->setModule('user');
            $editUser->setAction('edit-user');
            $editUser->setController('admin');
            $editUser->setRoute('default');
            $editUser->setResetParams(true);
            $users->addPage($editUser);
        }



        $logOut = new Zend_Navigation_Page_Mvc();
        $logOut->setLabel('Log out');
        $logOut->setModule('user');
        $logOut->setController('auth');
        $logOut->setAction('logout');
        $logOut->setRoute('default');
        $logOut->setResetParams(true);
        $navigation->addPage($logOut);



        $view->navigation($navigation);



    }

}