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

    public function subscriptionsSalesAction()
    {
        $start = $this->getRequest()->getParam('start', date("Y-m-d"));
        $end = $this->getRequest()->getParam('end', date("Y-m-d"));
        $type = $this->getRequest()->getParam('type', null);

        $list = Members_Model_DbTable_Subscription::getInstance()->getWeekSales($start, $end, $type);
        $this->view->list = $list;

    }

    public function emailsListAction() {
        $list = Members_Model_DbTable_Member::getInstance()->getQueryObject()->select('email, firstname, lastname')->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
        $this->view->list = $list;
        $stringParts = array();
        foreach($list as $data) {
            if(empty($data['email']))
                continue;
            $stringParts[] = $data['firstname'].' '.$data['lastname'].' <'.$data['email'].'>';
        }

        $this->view->stringData = implode(', ', $stringParts);

    }

    public function weekVisitsReportAction() {

        $week = $this->getRequest()->getParam('week', date("Y-W"));

        $parts = explode('-', $week);

        $visits = Members_Model_DbTable_Visit::getInstance()->getVisitsByWeek($parts[0], $parts[1]);

        $this->view->visits = $visits;
        $this->view->week = $week;



    }

}
