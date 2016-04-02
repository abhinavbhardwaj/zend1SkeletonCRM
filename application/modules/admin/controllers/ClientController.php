<?php

/**
 * @category   Client modules for
 * @Library    Zend FrameWork
 * @version     1.0'
 * @author: AbhinAV Bhardwaj
 * @since: March 27, 2016
 */
class Admin_ClientController extends Zend_Controller_Action {


    public function init() {
        parent::init();
        $layout = Zend_Layout::getMvcInstance(); //Create object
        $layout->setLayout('admin', true); //set layout admin
        require_once APPLICATION_PATH . '/../library/functions.php';
    }

    /**
     *Method to add product in The application
     */
    public function addAction(){
        $this->view->headTitle(ADMIN_ADD_CLIENT);
        $countryCode                    =       new Application_Model_DbTable_CountryCode();

        $this->view->header             =       "Add Client";
        //getting country List
        $this->view->country            =       $countryCode->getAllCountryCodes();
        $this->view->postData           =       array(); //default value for each field



        $this->_helper->viewRenderer('client/form', null, true);
        $request                        =       $this->getRequest();

        if($request->isPost()){
        $tblClient                      =       new Application_Model_DbTable_Clients();
        $postData                       =       $request->getPost();
        $this->view->postData           =       $postData;
        $postData['server_ip']          =       $_SERVER['REMOTE_ADDR'];
        $postData['created_date']       =       date("Y-m-d H:i:s");

        $addProduct                     =       $tblClient->addClient($postData);
        if($addProduct){
            $this->_helper->flashMessenger->addMessage('Client added successfully');
            $this->_redirect('/admin/client/list');
        }
        else{
            $this->_helper->flashMessenger->addMessage('Some Error occur please try again!');
            $this->_redirect('/admin/client/add');
        }
        }
    }

    /**
     *Function to Show all product
     */
    public function listAction(){
        $this->view->headTitle(ADMIN_CLIENT_LIST);
        $this->view->header             =       "View Clients";
        Zend_Layout::getMvcInstance()->assign('viewType', 'dataTable');//Telling Layout the we need datatable here
        $flashMessages                  =       $this->_helper->flashMessenger->getMessages();

        if (is_array($flashMessages) && !empty($flashMessages)) {
            $this->view->success        = $flashMessages[0];
        }
        //get All product List
        $tblClient                      =       new Application_Model_DbTable_Clients();
        $clients                        =       $tblClient->getAllClient(); 
        $this->view->clients            =       $clients;
    }
}