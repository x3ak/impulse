<?php
class Members_VisitController extends Zend_Controller_Action
{
    public function init()
    {
        if( $this->getRequest()->isXmlHttpRequest() ) {
            $this->_helper->layout->disableLayout();
        }
        parent::init();
    }

    /**
     * Visits dashboard
     */
    public function indexAction()
    {
        $todayList = Members_Model_DbTable_Visit::getInstance()->getDayVisitsList();
        $this->view->todayList = $todayList;
    }

    public function viewAction()
    {
        $id = $this->getRequest()->getParam('id');
        if(!empty($id)) {
            $mapper = Members_Model_DbTable_Visit::getInstance()->findOneBy('id', $id);
        }

        if(empty($mapper)) {
            $this->_helper->getHelper('FlashMessenger')->addMessage('visit_not_found');
            $this->_helper->redirector->goToRoute(array(
                                                     'module' => 'subscription',
                                                     'action' => 'list'
                                                ), 'admin', true);
        }

        $this->view->mapper = $mapper;
    }

    /**
     * Visits list
     */
    public function listAction()
    {
        $todayList = Members_Model_DbTable_Visit::getInstance()->getDayVisitsList();
        $this->view->todayList = $todayList;
    }

    public function newAction()
    {

        $form = new Members_Form_Visit_New();

        $memberId = $this->getRequest()->getParam('member');

        if(empty($memberId))
            throw new Exception('no member id');

        /** @var $member Members_Model_Mapper_Member */
        $member = Members_Model_DbTable_Member::getInstance()->findOneBy('id', $memberId);

        if(empty($member)) {
            throw new Exception('no member');
        }

        $form->setMember($member);





        $mapper = new Members_Model_Mapper_Visit();
        $mapper->fromArray($form->getValues());
        $mapper->save();

        $subscription = $mapper->Subscription;

        if($subscription->isPending()) {
            $subscription->activate();
        }

        $allowedVisitsPerWeek = $subscription->Type->visits_per_week;

        if(!empty($allowedVisitsPerWeek) ) {
            $weekVisits = $member->getCurrentWeekVisits();
            if($weekVisits > $allowedVisitsPerWeek) {

            }
        }


        $this->_helper->getHelper('FlashMessenger')->addMessage('visit_was_started');

        if(false === $this->getRequest()->isXmlHttpRequest() ) {
            $this->_helper->redirector->goToRoute(array(
                'module' => 'members',
                'action' => 'view',
                'id' => $memberId,
            ), 'admin', true);


        } else {
            $this->view->saved = true;
        }
    }

    public function finishAction()
    {
        $memberId = $this->getRequest()->getParam('member');

        if(empty($memberId))
            throw new Exception('no member id');

        /** @var $member Members_Model_Mapper_Member */
        $member = Members_Model_DbTable_Member::getInstance()->findOneBy('id', $memberId);

        if(empty($member)) {
            throw new Exception('no member');
        }

        $lastVisit = $member->getLastVisit();
        if(!empty($lastVisit) && empty($lastVisit->exit_time)) {
            $lastVisit->exit_time = date('H:i:s');
            $lastVisit->save();
        }

        $this->_helper->getHelper('FlashMessenger')->addMessage('visit_was_finished');

        if(false === $this->getRequest()->isXmlHttpRequest() ) {
            $this->_helper->redirector->goToRoute(array(
                'module' => 'members',
                'action' => 'view',
                'id' => $memberId,
            ), 'admin', true);


        } else {
            $this->view->saved = true;
        }

    }

    public function fastAction()
    {
        $memberNumber = $this->getRequest()->getParam('number');

        if(empty($memberNumber))
            throw new Exception('no member number');

        /** @var $member Members_Model_Mapper_Member */
        $member = Members_Model_DbTable_Member::getInstance()->findOneBy('number', $memberNumber);


        if(!empty($member)) {
            $lastVisit = $member->getLastVisit();
            $this->view->lastVisit = $lastVisit;
            $this->view->member = $member;
            $this->view->subscription = $member->getActiveSubscription();
        }



    }

}