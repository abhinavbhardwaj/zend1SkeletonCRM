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
    }

    public function deniedAction() {

    }

}
