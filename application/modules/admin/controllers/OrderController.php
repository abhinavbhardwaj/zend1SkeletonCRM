<?php
/**
 * @category Receipt modules for CRM
 * @Library  Zend FrameWork
 * @version  1.0
 * @author: Abhinav Bhardwaj
 * @since: March 27, 2016
 */
class Admin_OrderController extends Zend_Controller_Action {

     public function init() {
        parent::init();
        $layout = Zend_Layout::getMvcInstance(); //Create object
        $layout->setLayout('admin', true); //set layout admin
        require_once APPLICATION_PATH . '/../library/functions.php';
        $flashMessages                  =       $this->_helper->flashMessenger->getMessages(); //set flash message for all template

        if (is_array($flashMessages) && !empty($flashMessages)) {
            $this->view->success        = $flashMessages[0];
        }

    }


    /**
     *Index page where we will ask user which purchase order's detail they want to view
     */
    public function indexAction(){
          $this->view->headTitle(ADMIN_CLIENT_LIST);
          $this->view->header             =       "Order";
          $potbl                          =       new Application_Model_DbTable_PurchaseOrder();
          $this->view->product            =       $potbl->getAllPurchaseOrder();
          $this->_helper->viewRenderer('order/form', null, true);
          $request                        =       $this->getRequest();
          if($request->isPost()){
               $postData                  =       $request->getPost();
               $orderId                   =       $postData['poId'];
               //check where we have to redirect this request
               if($postData['type']=='challan')//send him on challan list for this purchase order
                    $this->_redirect('/admin/order/challan-list/orderId/'.$orderId);
               elseif($postData['type']=='invoice')//send him on challan list for this purchase order
                    $this->_redirect('/admin/order/invoice-list/orderId/'.$orderId);


          }
    }

    /*
     *Method to show all challan for purchase order
     */
    public function challanListAction(){
        $this->view->headTitle(ADMIN_PRODUCT_LIST);

        Zend_Layout::getMvcInstance()->assign('viewType', 'dataTable');//Telling Layout the we need datatable here
        $flashMessages                  =       $this->_helper->flashMessenger->getMessages();

        if (is_array($flashMessages) && !empty($flashMessages)) {
            $this->view->success        = $flashMessages[0];
        }
        //get All challan List
        $request                        =       $this->getRequest();
        $poId                           =       $request->getParam('orderId');
         $this->view->header             =       "View All Challan for $poId";
        $tblChallan                     =       new Application_Model_DbTable_Challan();
        $this->view->challan            =       $tblChallan->getAllChallanByPOId($poId);

    }

 /*
     *Method to show all challan for purchase order
     */
    public function invoiceListAction(){
        $this->view->headTitle(ADMIN_PRODUCT_LIST);

        Zend_Layout::getMvcInstance()->assign('viewType', 'dataTable');//Telling Layout the we need datatable here
        $flashMessages                  =       $this->_helper->flashMessenger->getMessages();

        if (is_array($flashMessages) && !empty($flashMessages)) {
            $this->view->success        = $flashMessages[0];
        }
        //get All challan List
        $request                        =       $this->getRequest();
        $poId                           =       $request->getParam('orderId');
        $this->view->header             =       "View All Invoice for $poId";
        $tblInvoice                     =       new Application_Model_DbTable_Invoice();
        $this->view->invoice            =       $tblInvoice->getAllInvoiceByPOId($poId);

    }
}