<?php

/**
 * @category   PSV Balance Admin modules
 * @package    PSV Balance
 * @subpackage Cms
 * @copyright  Copyright (c) A3logics India Ltd. (http://www.a3logics.com)
 * @Library    Zend FrameWork 1.11
 * @version    PSV Balance 1.0
 */
class Admin_LclockController extends Zend_Controller_Action {

    //private variables
    private $_lcLockDuration;

    /*
     * function call during initialization
     */

    public function init() {
        parent::init();
        $layout = Zend_Layout::getMvcInstance(); //Create object
        $layout->setLayout('admin', true); //set layout admin
        require_once APPLICATION_PATH . '/../library/functions.php';
        $this->_lcLockDuration = new Application_Model_DefaultLearningCustomization (); // creates object of class parent
    }

    /**
     * 
     */
    public function indexAction() {
        $this->view->headTitle(ADMIN_LEARNING_LOCK_DURATION);
        $request = $this->getRequest(); //creating object to get request
        $perPage = $request->getParam('perpage'); //getting param perpage
        if (!empty($perPage)) {
            $recordsPerPage = $perPage;
        } else {
            $recordsPerPage = PER_PAGE;
        }

        $flashMessages = $this->_helper->flashMessenger->getMessages();
        if (is_array($flashMessages) && !empty($flashMessages)) {
            $type = $flashMessages[1];
            if ($type == 'success') {
                $this->view->success = $flashMessages[0];
            } else {
                $this->view->error = $flashMessages[0];
            }
        }

        $lcData = $this->_lcLockDuration->getAllLcLockDuration(); //getting all faq data
        $totalRecords = count($lcData);
        $page = $this->_getParam('page', 1);
        $paginator = Zend_Paginator::factory($lcData);
        $paginator->setItemCountPerPage($recordsPerPage);
        $paginator->setCurrentPageNumber($page);
        // assigning the data to view file
        $this->view->lcData = $paginator;
        $this->view->totalRecord = $totalRecords;
        $this->view->currentPage = $page;
        $this->view->perpage = $perPage;
    }

    /**
     * function to add or update data
     * @param nill
     * @return Id
     */
    public function saveAction() {
        $request = $this->getRequest(); //creating object to get request
        $id = $request->getParam('id'); //getting param id
        $type = $request->getParam('type'); //getting param id

        $id = base64_decode($id); // decodes id
        $adminInfoSession = new Zend_Session_Namespace('adminInfo'); //creates instance of session

        if (!empty($id) && $id != null) {
            $editData = $this->_lcLockDuration->getLcLockDurationData($id); // getting data
            $this->view->editData = $editData; // assigning data to view file
            $this->view->headTitle(ADMIN_LEARNING_EDITLOCK_DURATION);
        }

        if ($type == 'Add' || $type == '') {
            $this->view->headTitle(ADMIN_LEARNING_ADDLOCK_DURATION);
            $lcqcData = $this->_lcLockDuration->getAllLcLockDuration();
            if (count($lcqcData) >= 10) {
                $this->_helper->getHelper('FlashMessenger')->addMessage("Maximum 10 entries can be added");
                $this->_helper->getHelper('FlashMessenger')->addMessage("error");
                $this->_redirect('admin/lclock');
            }
        }

        $flashMessages = $this->_helper->flashMessenger->getMessages();
        if (is_array($flashMessages) && !empty($flashMessages)) {
            $this->view->error = $flashMessages[0];
        }

        try {
            //if requestis post
            if ($request->isPost()) {
                $adminInfoSession->lclockData = $request->getPost();
                $id = $request->getPost('id');
                if (empty($id)) {
                    $id = null;
                }

                $duration = $request->getPost('duration');

                if ($duration == 0) {
                    $errorMessage = 'Duration cannot be zero';
                }

                if (validateNotNull($duration) == false) {
                    $errorMessage = 'Please enter duration';
                }

                if (validateNumber($duration) == false) {
                    $errorMessage = 'Please enter numeric value for duration';
                }

                //validates duplicacy of sortOrder
                if (empty($errorMessage) && $this->_lcLockDuration->checkLockDurationExistance($duration, $id) == true) {
                    $errorMessage = 'Duration already exist';
                }

                if (!empty($errorMessage) && $errorMessage != null) {
                    $this->_helper->getHelper('FlashMessenger')
                        ->addMessage($errorMessage);
                    if (!empty($id) && $id != null) {
                        $url = 'admin/lclock/save/id/' . base64_encode($id);
                    } else {
                        $url = 'admin/lclock/save';
                    }
                    $this->_redirect($url);
                }

                $data = array('duration' => $duration);
                if (!empty($id) && $id != null) {
                    $this->_lcLockDuration->updateLcLockDurationData($id, $data);
                    
                    $data = $this->_lcLockDuration->getLcLockDurationData($id);
                    if ($data->is_default) {
                        $this->_lcLockDuration->markDefaultLcLockDurationData($id);
                    }
                    
                    unset($adminInfoSession->lclockData);
                    $messAge = 'Data updated successfully';
                } else {
                    $this->_lcLockDuration->addLcLockDurationData($data);
                    unset($adminInfoSession->lclockData);
                    $messAge = 'Data added successfully';
                }

                $this->_helper->getHelper('FlashMessenger')->addMessage($messAge);
                $this->_helper->getHelper('FlashMessenger')->addMessage("success");
                $this->_redirect('admin/lclock');
            }
        } catch (Exception $e) {
            $this->view->error = $e->getMessage();
        }
    }

    /**
     * 
     */
    public function deleteAction() {
        $request = $this->getRequest(); //creating object to get request
        $lcData = $this->_lcLockDuration->getAllLcLockDuration(); //getting all faq data
        $totalRecords = count($lcData);
        if ($totalRecords <= 5) {
            $messAge = "There should be at least 5 entries";
            $this->_helper->getHelper('FlashMessenger')->addMessage($messAge);
            $this->_helper->getHelper('FlashMessenger')->addMessage("error");
            $this->_redirect('admin/lclock');
        } else {
            $id = $request->getParam('id'); //getting param id
            $data = $this->_lcLockDuration->getLcLockDurationData($id);
            if ($data->is_default) {
                $messAge = "Default entry cannot be deleted";
                $this->_helper->getHelper('FlashMessenger')->addMessage($messAge);
                $this->_helper->getHelper('FlashMessenger')->addMessage("error");
                $this->_redirect('admin/lclock');
            } else {
                try {
                    $delete = $this->_lcLockDuration->deleteLcLockDurationData($id);
                    if ($delete) {
                        $this->_helper->getHelper('FlashMessenger')->addMessage('Question count deleted successfully');
                        $this->_helper->getHelper('FlashMessenger')->addMessage("success");
                        $this->_redirect('admin/lclock');
                    }
                } catch (Exception $ex) {
                    $this->_helper->getHelper('FlashMessenger')->addMessage(' Error: ' . $ex->getMessage());
                    $this->_helper->getHelper('FlashMessenger')->addMessage("error");
                    $this->_redirect('admin/lclock');
                }
            }
        }
    }

    /**
     * 
     */
    public function defaultAction() {
        $request = $this->getRequest(); //creating object to get request
        $id = $request->getParam('id'); //getting param id
        try {
            $delete = $this->_lcLockDuration->markDefaultLcLockDurationData($id);
            if ($delete) {
                $this->_helper->getHelper('FlashMessenger')->addMessage('Question count marked default successfully');
                $this->_helper->getHelper('FlashMessenger')->addMessage("success");
                $this->_redirect('admin/lclock');
            }
        } catch (Exception $ex) {
            $this->_helper->getHelper('FlashMessenger')->addMessage(' Error: ' . $ex->getMessage());
            $this->_helper->getHelper('FlashMessenger')->addMessage("error");
            $this->_redirect('admin/lclock');
        }
    }

}
