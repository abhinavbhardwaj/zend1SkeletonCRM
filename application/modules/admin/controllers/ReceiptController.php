<?php
/**
 * @category Receipt modules for CRM
 * @Library  Zend FrameWork
 * @version  1.0
 * @author: Abhinav Bhardwaj
 * @since: March 27, 2016
 */
class Admin_ReceiptController extends Zend_Controller_Action {


    public function init() {
        parent::init();
        $layout = Zend_Layout::getMvcInstance(); //Create object
        $layout->setLayout('admin', true); //set layout admin
        require_once APPLICATION_PATH . '/../library/functions.php';
    }


   /**
     *Method to add product in The application
    */
    public function createChallanAction(){

        $this->view->headTitle(ADMIN_PURCHASE_ORDER);
        Zend_Layout::getMvcInstance()->assign('viewType', 'dateTime');//Telling Layout the we need datatable here

        $this->view->header             =       "VAT CHALLAN";
        $this->view->companyName        =       ADMIN_COMPANY_NAME ;
        $this->view->supplierOf         =       ADMIN_SUPPLIER_OF;
        $this->view->companyAddress     =       ADMIN_COMPANY_ADDRESS;
        $this->view->companyPhone       =       ADMIN_COMPANY_PHONE;
        $this->view->companyTin         =       ADMIN_COMPANY_TIN;
        $request                        =       $this->getRequest();
        $POid                           =       $request->getParam('orderId');

        if (is_array($flashMessages) && !empty($flashMessages)) {
            $this->view->success        = $flashMessages[0];
        }
        //get All product List
        $tblPO                          =       new Application_Model_DbTable_PurchaseOrder();
        $tblchallan                     =       new Application_Model_DbTable_Challan();
        $purchaseOrder                  =       $tblPO->getpurchaseOrderById($POid);

//prd($purchaseOrder[0]);
        $this->view->purchaseOrder      =       $purchaseOrder[0];
        $this->view->challanNo          =       $tblchallan->getChallanNo();
        if($request->isPost()){
        $tblPO                          =       new Application_Model_DbTable_PurchaseOrder();
        $postData                       =       $request->getPost();
        $this->view->postData           =       $postData;
        $POdata['server_ip']            =       $_SERVER['REMOTE_ADDR'];
        //first we add this into purchase order

        $addPOentry                     =       $tblPO->addPurchaseOrder($POdata);

        if($addPOentry > 0){
            //insert related entry into ordered product table
            $tblOrderProduct            =       new Application_Model_DbTable_OrderedProduct();



            $addPOentry                 =       $tblOrderProduct->addOrderedProduct($OPdata);
//delete that much amout of quentity from the product table --To be done

            $this->_helper->flashMessenger->addMessage('Order added successfully');
            $this->_redirect('/admin/receipt/print-purchase-order/orderId/'.$addPOentry);
        }
        else{
            $this->_helper->flashMessenger->addMessage('Some Error occur please try again!');
            $this->_redirect('/admin/client/add');
        }
        }
    }
   /**
     *Method to add product in The application
    */
    public function purchaseOrderAction(){

        $this->view->headTitle(ADMIN_PURCHASE_ORDER);
        Zend_Layout::getMvcInstance()->assign('viewType', 'dateTime');//Telling Layout the we need datatable here
        $clientTbl                      =       new Application_Model_DbTable_Clients();
        $productTbl                     =       new Application_Model_DbTable_Products();

        $this->view->header             =       "Purchase order";
        //getting client List
        $this->view->clients            =       $clientTbl->getAllActiveClients();
        $this->view->product            =       $productTbl->getAllProduct();
        $this->view->postData           =       array(); //default value for each field

        $request                        =       $this->getRequest();

        if($request->isPost()){
        $tblPO                          =       new Application_Model_DbTable_PurchaseOrder();
        $postData                       =       $request->getPost();
        $this->view->postData           =       $postData;
        $POdata['server_ip']            =       $_SERVER['REMOTE_ADDR'];
        //first we add this into purchase order
        //erssting the data for PO
        $POdata['client_id']            =       $postData['client_id'];
        $POdata['payment_date']         =       date("Y-m-d H:i:s",strtotime($postData['payment_date']));
        $POdata['delivery_date']        =       date("Y-m-d H:i:s",strtotime($postData['delivery_date']));
        $POdata['term']                 =       $postData['term'];
        $POdata['condition']            =       $postData['condition'];
        $POdata['order_for']            =       $postData['order_for'];
        $POdata['added_by']             =       $postData['added_by'];

        $addPOentry                     =       $tblPO->addPurchaseOrder($POdata);

        if($addPOentry > 0){
            //insert related entry into ordered product table
            $tblOrderProduct            =       new Application_Model_DbTable_OrderedProduct();
            $OPdata['po_id']            =       $addPOentry;
            $OPdata['product_id']       =       $postData['product_id'];
            $OPdata['ordered_quentity'] =       $postData['ordered_quentity'];
            $OPdata['given_quentity']   =       0;//Initially we have not given any amount of product this is just PO
            $OPdata['rate']             =       $postData['rate'];
            $OPdata['amount']           =       $postData['amount'];
            $OPdata['remark']           =       $postData['remark'];


            $addPOentry                 =       $tblOrderProduct->addOrderedProduct($OPdata);
//delete that much amout of quentity from the product table --To be done

            $this->_helper->flashMessenger->addMessage('Order added successfully');
            $this->_redirect('/admin/receipt/print-purchase-order/orderId/'.$addPOentry);
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
    public function printPurchaseOrderAction(){
        $this->view->headTitle(ADMIN_CLIENT_LIST);
        $this->view->header             =       "Purchase order";
        $flashMessages                  =       $this->_helper->flashMessenger->getMessages();
        $request                        =       $this->getRequest();
        $POid                           =       $request->getParam('orderId');

        if (is_array($flashMessages) && !empty($flashMessages)) {
            $this->view->success        = $flashMessages[0];
        }
        //get All product List
        $tblPO                          =       new Application_Model_DbTable_PurchaseOrder();
        $purchaseOrder                  =       $tblPO->getpurchaseOrderById($POid);


        $this->view->purchaseOrder      =       $purchaseOrder;
    }

    /**
     *Function to Show all product
     */
    public function allPurchaseOrderAction(){
        $this->view->headTitle(ADMIN_CLIENT_LIST);
        $this->view->header             =       "View All Purchase Order";
        Zend_Layout::getMvcInstance()->assign('viewType', 'dataTable');//Telling Layout the we need datatable here
        $flashMessages                  =       $this->_helper->flashMessenger->getMessages();

        if (is_array($flashMessages) && !empty($flashMessages)) {
            $this->view->success        = $flashMessages[0];
        }
        //get All product List
        $tblPO                          =       new Application_Model_DbTable_PurchaseOrder();
        $purchaseOrder                  =       $tblPO->getAllPurchaseOrder();
        //prd($purchaseOrder);
        $this->view->purchaseOrder      =       $purchaseOrder;
    }
}