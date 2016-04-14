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
        $flashMessages                  =       $this->_helper->flashMessenger->getMessages(); //set flash message for all template

        if (is_array($flashMessages) && !empty($flashMessages)) {
            $this->view->success        = $flashMessages[0];
        }

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

        $this->view->vat                =       VAT;
        $this->view->shipping           =       SHIPPING;
        $this->view->discount           =       DISCOUNT;

        $request                        =       $this->getRequest();
        $POid                           =       $request->getParam('orderId');

        //get All product List
        $tblPO                          =       new Application_Model_DbTable_PurchaseOrder();
        $tblchallan                     =       new Application_Model_DbTable_Challan();
        $purchaseOrder                  =       $tblPO->getpurchaseOrderById($POid);
 //prd($purchaseOrder);
        $this->view->purchaseOrder      =       $purchaseOrder[0];
        $this->view->challanNo          =       $tblchallan->getChallanNo();

        if($request->isPost()){
            $ChData                     =       $request->getPost();
            $this->view->postData       =       $ChData; //challan data
            $ChData['payment_date']     =       date("Y-m-d H:i:s", strtotime($ChData['payment_date']));
            $ChData['po_date']          =       date("Y-m-d H:i:s", strtotime($ChData['po_date']));
            $ChData['bill_date']        =       date("Y-m-d H:i:s", strtotime($ChData['bill_date']));
            $ChData['created_date']     =       date("Y-m-d H:i:s");
            $Opdata['rate']             =       $ChData['rate'];
            unset($ChData['rate']);
            //first we need to create chalan
             $addChallan                =       $tblchallan->insert($ChData);



        if($addChallan > 0){

            //we also need Order Product module
            $tblOP                      =       new Application_Model_DbTable_OrderedProduct();


            $Opdata['given_quentity']   =       (int)($ChData['quantity']+$purchaseOrder[0]['given_quentity']); //Order product data setup


            //Set Purchase order
            $remaningQuentity           =       ((int) $purchaseOrder[0]['ordered_quentity'] - $Opdata['given_quentity']);

            if($remaningQuentity == $purchaseOrder[0]['ordered_quentity'] ) //nothing has been chnaged so order is still open
                $status                 =        "open";
            else if($remaningQuentity == 0 ) //Nothing has been remaning so lets complete this
                $status                 =        "complete";
            else
                $status                 =        "in-progress";

            $pOdata['status']           =       $status;//Purchase order status

            $tblOP->updateData($Opdata, $ChData['order_product_id']);//update Ordered product quentity
            $tblPO->updateData($pOdata, $ChData['order_no']);//update purchase order status

            $this->_helper->flashMessenger->addMessage('Order added successfully');
            $this->_redirect('/admin/receipt/print-challan/challanId/'.$addChallan);
        }
        else{
            $this->_helper->flashMessenger->addMessage('Some Error occur please try again!');
            $this->_redirect('/admin/receipt/all-purchase-order');
        }



        }
    }

/**
     *Method to add product in The application
    */
    public function createInvoiceAction(){

        $this->view->headTitle(ADMIN_PURCHASE_ORDER);
        Zend_Layout::getMvcInstance()->assign('viewType', 'dateTime');//Telling Layout the we need datatable here
        $this->view->header             =       "VAT INVOICE";
        $this->view->companyName        =       ADMIN_COMPANY_NAME ;
        $this->view->supplierOf         =       ADMIN_SUPPLIER_OF;
        $this->view->companyAddress     =       ADMIN_COMPANY_ADDRESS;
        $this->view->companyPhone       =       ADMIN_COMPANY_PHONE;
        $this->view->companyTin         =       ADMIN_COMPANY_TIN;

        $this->view->vat                =       VAT;
        $this->view->shipping           =       SHIPPING;
        $this->view->discount           =       DISCOUNT;

        $request                        =       $this->getRequest();
        $POid                           =       $request->getParam('orderId');

        //get All product List
        $tblPO                          =       new Application_Model_DbTable_PurchaseOrder();
        $tblchallan                     =       new Application_Model_DbTable_Challan();
        $tblinvoice                     =       new Application_Model_DbTable_Invoice();

        $purchaseOrder                  =       $tblPO->getpurchaseOrderById($POid);
        $challanDetails                 =       $tblchallan->getAllChallanByPOId($POid);
        $this->view->purchaseOrder      =       $purchaseOrder[0];
        $this->view->invoiceNo          =       $tblinvoice->getInvoiceNo();
        $this->view->challan            =       $challanDetails;

        if($request->isPost()){
            $ChData                     =       $request->getPost();
            $this->view->postData       =       $ChData; //challan data

            $ChData['challan_ids']      =       implode(",",$ChData['challan_ids']);
            $ChData['payment_date']     =       date("Y-m-d H:i:s", strtotime($ChData['payment_date']));
            $ChData['po_date']          =       date("Y-m-d H:i:s", strtotime($ChData['po_date']));
            $ChData['gr_date']          =       date("Y-m-d H:i:s", strtotime($ChData['gr_date']));
            $ChData['created_date']     =       date("Y-m-d H:i:s");

            //first we need to create chalan
             $addInvoice                =       $tblinvoice->addInvoice($ChData);



        if($addInvoice > 0){

            $this->_helper->flashMessenger->addMessage('Invoice saved successfully');
            $this->_redirect('/admin/receipt/print-invoice/invoiceId/'.$addInvoice);
        }
        else{
            $this->_helper->flashMessenger->addMessage('Some Error occur please try again!');
            $this->_redirect('/admin/receipt/all-purchase-order');
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
        $POdata['created_date']         =       date("Y-m-d H:i:s");

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
            $this->_redirect('/admin/receipt/all-purchase-order');
        }
        }
    }


    /**
     *Function to Show Purchase order detail to take a print out
     */
    public function printPurchaseOrderAction(){
        $this->view->headTitle(ADMIN_CLIENT_LIST);
        $this->view->header             =       "Purchase order";
        $request                        =       $this->getRequest();
        $POid                           =       $request->getParam('orderId');


        //get All product List
        $tblPO                          =       new Application_Model_DbTable_PurchaseOrder();
        $purchaseOrder                  =       $tblPO->getpurchaseOrderById($POid);


        $this->view->purchaseOrder      =       $purchaseOrder;
    }

     /**
     *Function to Show Invoice detail to take a print out
     */
    public function printInvoiceAction(){
        $this->view->headTitle(ADMIN_CLIENT_LIST);
        $this->view->header             =       "VAT INVOICE";
        $this->view->companyName        =       ADMIN_COMPANY_NAME ;
        $this->view->supplierOf         =       ADMIN_SUPPLIER_OF;
        $this->view->companyAddress     =       ADMIN_COMPANY_ADDRESS;
        $this->view->companyPhone       =       ADMIN_COMPANY_PHONE;
        $this->view->companyTin         =       ADMIN_COMPANY_TIN;

        $this->view->vat                =       VAT;
        $this->view->shipping           =       SHIPPING;
        $this->view->discount           =       DISCOUNT;

        $request                        =       $this->getRequest();
        $InId                           =       $request->getParam('invoiceId');

        //get All Invoice detail
        $tblPO                          =       new Application_Model_DbTable_PurchaseOrder();
        $tblchallan                     =       new Application_Model_DbTable_Challan();
        $tblinvoice                     =       new Application_Model_DbTable_Invoice();
        $invoice                        =       $tblinvoice->getInvoiceById($InId);
        $invoice                        =       $invoice[0];


        $POid                           =       $invoice['order_no'];
        $purchaseOrder                  =       $tblPO->getpurchaseOrderById($POid);
        $challanDetails                 =       $tblchallan->getAllChallanByPOId($POid);
        $this->view->purchaseOrder      =       $purchaseOrder[0];
        $this->view->invoiceNo          =       $invoice['id'];
        $this->view->challan            =       $challanDetails;
        $this->view->invoice            =       $invoice;
    }

    /**
     *Function to Show  Challan detail to take a print out
     */
    public function printChallanAction(){
        $this->view->headTitle(ADMIN_CLIENT_LIST);
        $this->view->header             =       "VAT CHALLAN";
        $this->view->companyName        =       ADMIN_COMPANY_NAME ;
        $this->view->supplierOf         =       ADMIN_SUPPLIER_OF;
        $this->view->companyAddress     =       ADMIN_COMPANY_ADDRESS;
        $this->view->companyPhone       =       ADMIN_COMPANY_PHONE;
        $this->view->companyTin         =       ADMIN_COMPANY_TIN;

        $this->view->vat                =       VAT;
        $this->view->shipping           =       SHIPPING;
        $this->view->discount           =       DISCOUNT;

        $request                        =       $this->getRequest();
        $chId                           =       $request->getParam('challanId');


        //get All product List
        $tblCh                          =       new Application_Model_DbTable_Challan();
        $challan                        =       $tblCh->getChallanById($chId);
        $this->view->challan            =       $challan;
    }

    /**
     *Function to Show all product
     */
    public function allPurchaseOrderAction(){
        $this->view->headTitle(ADMIN_CLIENT_LIST);
        $this->view->header             =       "View All Purchase Order";
        Zend_Layout::getMvcInstance()->assign('viewType', 'dataTable');//Telling Layout the we need datatable here


        //get All product List
        $tblPO                          =       new Application_Model_DbTable_PurchaseOrder();
        $purchaseOrder                  =       $tblPO->getAllPurchaseOrder();
        //prd($purchaseOrder);
        $this->view->purchaseOrder      =       $purchaseOrder;
    }
}