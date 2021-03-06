<?php
class ErrorController extends Zend_Controller_Action {

	public function  init()
	{
		$ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('error', 'html')
                    ->initContext('html');
	}


	public function errorAction()
	{
        $errors = $this->_getParam('error_handler');
        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $this->view->message = 'Page not found';
                break;
            default:
                // application error
                $this->getResponse()->setHttpResponseCode(500);
                $this->view->message = 'Application error';
                break;
        }

        if(!empty($errors->exception))
            $this->view->exceptions = array($errors->exception);
        else
            $this->view->exceptions = $errors->exceptions;
        $this->view->request   = $errors->request;
    }
    public function error404Action() {

    }
}