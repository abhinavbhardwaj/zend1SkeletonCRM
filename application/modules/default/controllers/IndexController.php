<?php
/*
 *First Class which handel user request
 *@author: Abhinav
 *@since:April 2, 2016
 */
class  IndexController extends Zend_Controller_Action{
    
    //    public function init() {
    //    parent::init();
    //    //we don't have front end for now so lets send the request to admin
    //    $this->_redirect('admin/index/index');
    //}
    /*
     * function for when admin page request then redirect on login page for authentication
     */

    public function indexAction() {
        $this->_redirect('admin/login/login');
    }
}
