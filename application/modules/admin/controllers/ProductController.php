<?php

/**
 * @category   PSV Balance Admin modules
 * @Library    Zend FrameWork 1.11
 * @version    PSV Balance 1.0
 */
class Admin_ProductController extends Zend_Controller_Action {


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
        $this->view->headTitle(ADMIN_ADD_PRODUCT);

        $this->view->header             =       "Add Products";
        $this->view->productName        =       NULL;
        $this->view->productUnit        =       NULL;
        $this->view->productStock       =       NULL;
        $this->view->defaultUnits       =       array("Litre","Kg", "Bottel");

        $this->_helper->viewRenderer('product/form', null, true);
        $request                        =       $this->getRequest();

        if($request->isPost()){

        $this->view->productName        =       $postData['name'];
        $this->view->productUnit        =       $postData['unit'];
        $this->view->productStock       =       $postData['stock'];

        $tblProduct                     =       new Application_Model_DbTable_Products();
        $postData                       =       $request->getPost();
        $postData['server_ip']          =       $_SERVER['REMOTE_ADDR'];

        $addProduct                     =       $tblProduct->addProduct($postData);
        if($addProduct){
            $this->_helper->flashMessenger->addMessage('Product added successfully');
            $this->_redirect('/admin/product/list');
        }
        else{
            $this->_helper->flashMessenger->addMessage('Some Error occur please try again!');
            $this->_redirect('/admin/product/add');
        }
        }
    }

    /**
     *Function to Show all product
     */
    public function listAction(){
        $this->view->headTitle(ADMIN_PRODUCT_LIST);
        $this->view->header             =       "View Products";
        Zend_Layout::getMvcInstance()->assign('viewType', 'dataTable');//Telling Layout the we need datatable here
        $flashMessages                  =       $this->_helper->flashMessenger->getMessages();

        if (is_array($flashMessages) && !empty($flashMessages)) {
            $this->view->success        = $flashMessages[0];
        }
        //get All product List
        $tblProduct                     =       new Application_Model_DbTable_Products();
        $products                       =       $tblProduct->getAllProduct();
        $this->view->products           =       $products;
    }
}
