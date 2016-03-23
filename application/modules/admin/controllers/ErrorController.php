<?php

class Admin_ErrorController extends Zend_Controller_Action
{

    public function errorAction()
    {

        $errors = $this->_getParam('error_handler');

        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $this->view->message = 'Page not found';
                $this->view->flag = true;
                break;
            default:
                // application error
                $this->getResponse()->setHttpResponseCode(500);
                $this->view->message = 'Application error';
                $this->view->flag = false;
                $this->logError($errors);
                break;
        }

        // conditionally display exceptions
        if ($this->getInvokeArg('displayExceptions') == true) {
            $this->view->exception = $errors->exception;
        }

        $this->view->request = $errors->request;
    }

    public function logError($errors)
    {
        //block for generate log files
        $fileNamePath = APPLICATION_PATH . '/../logs/';
        $filename = $fileNamePath . 'error.log';

        if (file_exists($filename) && filesize($filename) > 10000000) {
            rename($filename, $fileNamePath . 'error' . date('dmYhis') . '.log');
        }

        $writer = new Zend_Log_Writer_Stream($filename);
        $logger = new Zend_Log($writer);
        $logger->info(
                $errors->exception .
                PHP_EOL . print_r($errors->request->getParams(), TRUE) .
                PHP_EOL . '-----------------------------------------------------------' .
                PHP_EOL
        );
    }

}
