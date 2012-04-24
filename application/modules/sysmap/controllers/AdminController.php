<?php
/**
 * SlyS
 *
 * Created by Serghei Ilin <criolit@gmail.com>
 * User: criolit
 * Date: 29.12.10
 * Time: 11:10
 */

/**
 * Sysmap admin controller
 */
class Sysmap_AdminController extends Zend_Controller_Action
{
    /**
     * @var Sysmap_Model_Map
     */
    protected $_mapModel;

    /**
     * Per page for list
     * @var int
     */
    protected $_perPage = 20;

    public function init()
    {
        $this->_mapModel = Sysmap_Model_Map::getInstance();
    }

    public function indexAction()
    {
        $this->_forward('list');
    }

    /**
     * List the map
     *
     * Shows the list of the map
     * in hierarchy
     *
     * @return void
     */
    public function listAction()
    {
        $this->_mapModel->reindexMCA();
        $this->view->sysmapTree = $this->_mapModel->getMapTree(array('id', 'title', 'mca', 'is_pattern'))->fetchTree(array(), Doctrine_Core::HYDRATE_ARRAY);
    }

    /**
     * @return void
     */
    public function editExtendAction()
    {
        $form = new Sysmap_Form_Extend();
        $form->getElement('sysmap_id')->setValueKey('id');

        if ( $this->getRequest()->isPost() ) {
            if ( $form->isValid( $this->getRequest()->getPost() ) ) {
                $this->_mapModel->addExtend( $form->getValues() );
                return $this->_helper->redirector->gotoUrl( $this->view->url( array('module' => 'sysmap', 'controller' => 'admin', 'action' => 'list'), null, true ) );
            }
        }
        else {
            $extendId = $this->getRequest()->getParam('id');
            if (empty($extendId) === false) {
                $mapItem = Sysmap_Model_DbTable_Sysmap::getInstance()->findOneBy('id', $extendId);

                if (empty($mapItem) === false) {
                    $values = $mapItem->toArray();
                    $values['sysmap_id'] = $mapItem->getNode()->getParent()->id;
                    $form->populate($values);
                }
            }
        }

        $this->view->editExtensionForm = $form;
    }

    public function deleteExtendAction()
    {
        $id = $this->getRequest()->getParam('id');

        if (empty($id) === false) {
            $object = Sysmap_Model_DbTable_Sysmap::getInstance()->findOneBy('id', $id);
            $hash = $object->hash;

            if (empty($object) === false) {
                $object->getNode()->delete();
                Slys_Api::getInstance()->notify(null, 'sysmap.item-deleted', array('identifier' => $hash));
            }
        }

        return $this->_helper->redirector->gotoUrl( $this->view->url( array('module' => 'sysmap', 'controller' => 'admin', 'action' => 'list'), null, true ) );
    }

    public function editExtendPatternAction()
    {
        $form = new Sysmap_Form_ExtendPattern();

        if ( $this->getRequest()->isPost() ) {
            if ( $form->isValid( $this->getRequest()->getPost() ) ) {
                $this->_mapModel->addExtendPattern( $form->getValues() );
                return $this->_helper->redirector->gotoUrl( $this->view->url( array('module' => 'sysmap', 'controller' => 'admin', 'action' => 'list'), null, true ) );
            }
        }
        else {
            $extendId = $this->getRequest()->getParam('id');

            if (empty($extendId) === false) {
                $mapItem = Sysmap_Model_DbTable_Sysmap::getInstance()->findOneBy('id', $extendId);

                if (empty($mapItem) === false) {
                    $values = $mapItem->toArray();

                    $mca = explode('.', $values['mca']);

                    $values['mca_module'] = $mca[0];
                    $values['mca_controller'] = $mca[1];
                    $values['mca_action'] = $mca[2];

                    $values['sysmap_id'] = $mapItem->getNode()->getParent()->id;

                    $form->populate($values);
                }
            }
        }

        $this->view->editExtensionPatternForm = $form;
    }
}