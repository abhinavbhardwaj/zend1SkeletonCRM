<?php
/**
 * @category     Admin modules
 * @package    CRM
 * @subpackage Login
 * @copyright  Copyright (c) Abhinav Bhardwaj 
 * @Library    Zend FrameWork 1.11
 * @version    CRM 1.0
 */

/**
 * Concrete base class for About classes
 *
 *
 * @uses       IndexController
 * @category   Index
 * @package    Zend_Application
 * @subpackage Index
 */
class Admin_IndexController extends Zend_Controller_Action {

    public function init() {
        parent::init();
        $layout = Zend_Layout::getMvcInstance(); //Create object
        $layout->setLayout('admin', true); //set layout admin
    }

    /*
     * function for when admin page request then redirect on login page for authentication
     */

    public function indexAction() {
        $this->_redirect('admin/login/login');
    }

    public function dashboardAction() {
        $objParent = new Application_Model_Parents();
        $objChild = new Application_Model_Child();
        $objQuestion = new Application_Model_Question();
        $objApps = new Application_Model_Apps();
        
        $this->view->parentChildCount = $objChild->getAllParentwithChildCount();
        $this->view->totalParents = $objParent->getTotalFinnyParent();
        $this->view->totalFinnyUsers = $objChild->getTotalFinnyChild();
        $this->view->questionData = $objQuestion->getTotalQuestions();
        $this->view->appsLog = $objApps->getLogForAllAppsUsedByChild();
        $flashMessages 			= $this->_helper->flashMessenger->getMessages();
        $flashMessenger 		= $this->_helper->getHelper('FlashMessenger');
        $flashMessages 			= $flashMessenger->getMessages();
        if(is_array($flashMessages) && !empty($flashMessages)) {
        	$this->view->success = $flashMessages[0];
        	$flashMessenger->addMessage('');
        }
        
      //  echo '<pre>';print_r(json_decode($this->view->parentChildCount, true));exit;
    }

    public function deniedAction() {
        
    }

}
