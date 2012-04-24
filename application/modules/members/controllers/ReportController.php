<?php
/**
 * Members Report controller
 */
class Members_ReportController extends Zend_Controller_Action
{
    public function init()
    {
        if( $this->getRequest()->isXmlHttpRequest() ) {
            $this->_helper->layout->disableLayout();
        }

        parent::init();
    }

    /**
     * Dashboard
     */
    public function indexAction()
    {

    }

    public function subscriptionsChartAction()
    {


    }

    public function emailsListAction() {
        $list = Members_Model_DbTable_Member::getInstance()->getQueryObject()->select('email, firstname, lastname')->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
        $this->view->list = $list;
        $stringParts = array();
        foreach($list as $data) {
            $stringParts[] = $data['firstname'].' '.$data['lastname'].' <'.$data['email'].'>';
        }

        $this->view->stringData = implode(', ', $stringParts);

    }

}
