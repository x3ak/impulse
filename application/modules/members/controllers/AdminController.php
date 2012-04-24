<?php
/**
 * Members administration
 */
class Members_AdminController extends Zend_Controller_Action
{
    public function init()
    {
        if( $this->getRequest()->isXmlHttpRequest() ) {
            $this->_helper->layout->disableLayout();
        }

        $this->view->headLink()->appendStylesheet('/themes/default/css/admin/members.css');
        parent::init();
    }

    /**
     * Members dashboard
     */
    public function indexAction()
    {
        $this->_forward('list');
    }

    /**
     * Members list
     */
    public function listAction()
    {
        $page = $this->getRequest()->getParam('page', 1);
        $orderField = $this->getRequest()->getParam('order-by', 'number');
        $orderDirection = $this->getRequest()->getParam('order-dir', 'desc');

        $savedFilter = $this->getRequest()->getParam('saved-filter', 'all');


        $returnAsJSON = (boolean) $this->getRequest()->getParam('return-as-json', false);

        $filterNumber = $this->getRequest()->getParam('number');
        $filterName = $this->getRequest()->getParam('name');

        $listDQL = Members_Model_DbTable_Member::getInstance()->getList($page, $orderField, $orderDirection);

        if(!empty($filterName)) {
            $listDQL->where("lower(m.firstname) LIKE lower(?) OR lower(m.lastname) LIKE lower(?)", array('%'.$filterName.'%', '%'.$filterName.'%'));
        }

        if(!empty($filterNumber)) {
            $listDQL->where('m.number LIKE ?', $filterNumber.'%');
        }


        switch($savedFilter) {
            case "active":
                $listDQL->innerJoin('m.Subscriptions s WITH s.status = ?', 'ACTIVE');
                break;
            case "todays":
                $listDQL->innerJoin('m.Visits v WITH v.day = ?', date('Y-m-d'));
                break;
            case "expired":
                $listDQL->leftJoin('m.Subscriptions s');
                $listDQL->where('s.status != "ACTIVE" OR s.status IS NULL ');
                break;
            case "expire-in-1-week":
                $listDQL->innerJoin('m.Subscriptions s WITH s.expire_date < ? AND expire_date > ?', array(date('Y-m-d', strtotime('+1 week')), date('Y-m-d')));
                break;
            case "inside":
                $listDQL->innerJoin('m.Visits v WITH v.exit_time IS NULL');
                break;


            default:

        }


        $this->view->list = $listDQL->execute();

        $this->view->moreResultsExists = $listDQL->limit(Members_Model_DbTable_Member::$perPage+1)->execute()->count() > Members_Model_DbTable_Member::$perPage;


        $this->view->page = $page;

        $this->view->number = $filterNumber;
        $this->view->name = $filterName;

        $this->view->savedFilter = $savedFilter;


        if($returnAsJSON) {
            echo json_encode($this->view->list->toArray());
            die();
        }

        if( $this->getRequest()->isXmlHttpRequest() ) {
            $this->renderScript('ajax/members-list.phtml');
        }


    }

    /**
     * Edit / Add member
     */
    public function editAction()
    {
        $form = new Members_Form_Edit();

        $id = $this->getRequest()->getParam('id');

        if(!empty($id))
            $memberMapper = Members_Model_DbTable_Member::getInstance()->findOneBy('id', $id);

        if(empty($memberMapper)) {
            $memberMapper = new Members_Model_Mapper_Member();
            $this->view->new_member = true;
        }

        $form->populate($memberMapper->toArray());

        if($this->getRequest()->isPost()) {
            if($form->isValid($this->getRequest()->getPost())) {
                $memberMapper->fromArray($form->getValues());
                $memberMapper->save();

                if($form->photo->receive()) {

                    $filename = $form->photo->getFileName();

                    rename($filename,dirname($filename).DIRECTORY_SEPARATOR.$memberMapper->id.'.jpg');
                }

                $this->_helper->getHelper('FlashMessenger')->addMessage('member_saved');
                $this->_helper->redirector->goToRoute(array(
                                                            'module' => 'members',
                                                            'controller' => 'admin',
                                                            'action' => 'view',
                                                            'id' => $memberMapper->id
                                                       ), 'admin', true);
            }
        }

        $this->view->member = $memberMapper;
        $this->view->form = $form;
    }

    public function viewAction()
    {
        $id = $this->getRequest()->getParam('id');

        if(!empty($id))
            $memberMapper = Members_Model_DbTable_Member::getInstance()->findOneBy('id', $id);

        if(empty($memberMapper)) {
            $this->_helper->getHelper('FlashMessenger')->addMessage('member_not_found');
            $this->_helper->redirector->goToRoute(array(
                                                        'module' => 'members',
                                                        'controller' => 'admin',
                                                        'action' => 'list'
                                                   ), 'admin', true);
        }

        $this->view->member = $memberMapper;
    }

    public function addSubscriptionAction()
    {
        $id = $this->getRequest()->getParam('id');

        if(!empty($id)) {
            /** @var $memberMapper Members_Model_Mapper_Member */
            $memberMapper = Members_Model_DbTable_Member::getInstance()->findOneBy('id', $id);
        }

        if(empty($memberMapper)) {
            $this->_helper->getHelper('FlashMessenger')->addMessage('member_not_found');
            $this->_helper->redirector->goToRoute(array(
                                                        'module' => 'members',
                                                        'controller' => 'admin',
                                                        'action' => 'list'
                                                   ), 'admin', true);
        }

        $this->view->member = $memberMapper;
        $typeId = $this->getRequest()->getParam('type');
        if(!empty($typeId)) {
            /** @var $typeMapper Members_Model_Mapper_SubscriptionType */
            $typeMapper = Members_Model_DbTable_SubscriptionType::getInstance()->findOneBy('id', $typeId);

            if(!empty($typeMapper)) {

                $lastSubscription = $memberMapper->getLastSubscription();

                $link = new Members_Model_Mapper_Subscription();
                $link->Member = $memberMapper;
                $link->Type = $typeMapper;
                $link->price_on_signup = $typeMapper->price;

                if(!empty($lastSubscription)) {
                    $link->status = 'PENDING';
                    $link->start_date = date('Y-m-d', strtotime('+1 day', strtotime($lastSubscription->expire_date)));
                    $newExpireDate = strtotime("+". $lastSubscription->Type->duration . ' ' . $lastSubscription->Type->units , strtotime($link->start_date));
                    $link->expire_date = date('Y-m-d', $newExpireDate);
                }

                $link->save();

                if(empty($lastSubscription)) {
                    $link->activate(); //immediately activate if this was first subscription
                }

                $this->_helper->getHelper('FlashMessenger')->addMessage('subscription_added');
                $this->_helper->redirector->goToRoute(array(
                                                            'module' => 'members',
                                                            'action' => 'view',
                                                            'id' => $id
                                                       ), 'admin', true);
            }
        }

        $types = Members_Model_DbTable_SubscriptionType::getInstance()->findAll();
        $this->view->types = $types;
    }
}
