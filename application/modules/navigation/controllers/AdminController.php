<?php
/**
 * Navigation administrative controller
 *
 * @version    $Id: AdminController.php 1136 2011-01-28 15:50:56Z criolit $
 */
class Navigation_AdminController extends Zend_Controller_Action
{
	/**
	 * @var Navigation_Model_Navigation
	 */
	protected $_navigationModel = null;

	public function init()
	{
		$this->_navigationModel = Navigation_Model_Navigation::getInstance();
	}

    /**
     *
     * @return void
     */
	public function indexAction()
	{
		$this->_forward('list-menu');
	}

	/**
	 * List structured menu
	 */
	public function listMenuAction()
	{
		$this->view->tree = $this->_navigationModel->getStructureTree(array('*'))->fetchTree(array(), Doctrine_Core::HYDRATE_ARRAY);

        $apiRequest = new Slys_Api_Request($this, 'user.get-acl');
        $acl = $apiRequest->proceed()->getResponse()->getFirst();

        $this->view->acl = $acl;
	}

    /**
	 * Move menu item
	 */
	public function moveAction()
	{
		$direction = $this->getRequest()->getParam('dir','down');
        $itemId = $this->getRequest()->getParam('id', null);
        $item = $this->_navigationModel->getItem($itemId);

        switch($direction) {
            case 'up':
                if($item->getNode()->hasPrevSibling()) {
                    $prevSib = $item->getNode()->getPrevSibling();
                    $item->getNode()->moveAsPrevSiblingOf($prevSib);
                }
                break;
            case 'down':
            default:
                if($item->getNode()->hasNextSibling()) {
                    $nextSib = $item->getNode()->getNextSibling();
                    $item->getNode()->moveAsNextSiblingOf($nextSib);
                }

        }

        $this->_navigationModel->clearCache();

        $this->_helper->getHelper('FlashMessenger')->addMessage('move_ok');
        $this->_helper->redirector->goToRoute(array(
                                                    'module' => 'navigation',
                                               ), 'admin', true);
	}

	/**
	 * Editing menu item (leaf node)
	 */
	public function editMenuItemAction()
	{
		$id = $this->getRequest()->getParam('id', null);
		if ($id !== null) {
            $item = $this->_navigationModel->getItem($id);
			if (empty($item) === false and $item->read_only === true) {
				$this->_helper->getHelper('FlashMessenger')->addMessage('That was a READ ONLY navigation item');
				return $this->_helper->redirector->gotoUrl(
				    $this->view->url( array('action' => 'list-menu') )
                );
			}
		}

		$form = new Navigation_Form_MenuItem();

		if ($this->getRequest()->isPost()) {
			if ($form->isValid($this->getRequest()->getPost())) {
				$this->_navigationModel->saveLeafItem( $form->getValues() );
				return $this->_helper->redirector->gotoUrl(
				    $this->view->url( array('action' => 'list-menu', 'module' => 'navigation', 'controller' => 'admin'), null, true )
                );
			}
		}
		else {
			if ($id !== null) {
				$itemData = $this->_navigationModel->getItem($id);
                if (empty($itemData) === false) {
                    $correctParentId = $itemData->getNode()->getParent()->id;

                    $itemData = $itemData->toArray();
                    $itemData['parent_id'] = $correctParentId;

                    $form->populate($itemData);
                }
                else {
                    return $this->_helper->redirector->gotoUrl(
				        $this->view->url( array('action' => 'edit-menu-item', 'module' => 'navigation', 'controller' => 'admin'), null, true )
                    );
                }
			}
		}

		$this->view->menuItemForm = $form;
	}

	/**
	 * Deleting menu node
	 */
	public function deleteMenuItemAction()
	{
		$id = $this->getRequest()->getParam('id');

		if ($id !== null) {
			if ($this->_navigationModel->getItem($id)->read_only === true) {
				$this->_helper->getHelper('FlashMessenger')->addMessage('That was a READ ONLY navigation item');
				return $this->_helper->redirector->gotoUrl(
				    $this->view->url( array('action' => 'list-menu') )
                );
			}
		}
		else {
			$this->_helper->getHelper('FlashMessenger')->addMessage('No value for ID parameter');
			return $this->_helper->redirector->gotoUrl( $this->view->url( array('action' => 'list-menu') ) );
		}

		$this->_navigationModel->deleteItem($id);

		return $this->_helper->redirector->gotoUrl(
		    $this->view->url( array('action' => 'list-menu') )
		);
	}
}