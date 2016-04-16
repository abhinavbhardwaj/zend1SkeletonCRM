<?php
/*
 * This is a model class for Invoice
 * Created By Abhinav Bhardwaj
 *
 */
class Application_Model_DbTable_Invoice extends Zend_Db_Table_Abstract
{
	// This is name of Table
	protected $_name = 'bal_invoice';




	//function to add categories
	public function addInvoice($data)
	{
		$insertId 		= $this->insert($data);
		return $insertId;

	}

	/**
	 *Function to get invoice by  Id
	 */
	public function getInvoiceById($invoiceId){
		$db 				= 		Zend_Db_Table::getDefaultAdapter();
		$select 			= 		$db->select()
									->from(array('in' => $this->_name), "in.*")
									->where("in.id =  $invoiceId");
		$poInfo 			=  		$db->fetchAll($select);

      return $poInfo;
	}

	/**
	 *Function to get All Invoice by purchase order Id
	 */
	public function getAllInvoiceByPOId($POId){
		$db 				= 		Zend_Db_Table::getDefaultAdapter();
		$select 			= 		$db->select()
									->from(array('in' 			=> 	$this->_name),
										   array("invoice_id"	=>	"in.id",
												 "invoice_no"	=>	"in.invoice_no",
												 "payment_date"	=>	"in.payment_date",
												 "gr_no"		=>	"in.gr_no",
												 "challan_ids"	=>	"in.challan_ids",
												 "total"		=>	"in.total",
												 ))
									->joinLeft(array('op' 		=>  'bal_ordered_product'),
										'in.order_no = op.id',
										array(
											  "given_quentity"	=>	"op.given_quentity",
											  "ordered_quentity"=>	"op.ordered_quentity",
											  "rate"			=>	"op.rate"
											  )
										)
									->joinLeft(array('cli' 		=> 	'bal_clients'),
										'cli.id = in.client_id',
										array(
											  "client_company_name"=>"cli.company_name",
											  "client_name"		=>	"cli.name"
											  )
										)

									->where("in.order_no =  $POId");
		$chInfo 			=  		$db->fetchAll($select);

      return $chInfo;
	}

	/**
	 *Function to get Invoice number
	 *Here we are just taking higest chalan id from challan table
	 */
	public function getInvoiceNo(){
		$db 				= 		Zend_Db_Table::getDefaultAdapter();
		$select 			= 		$db->select()->from($this->_name, array(new Zend_Db_Expr("MAX(id) AS maxID")));

		$maxId				=		((int)$db->fetchOne($select)+1);
		$maxId				=		($maxId == 1) ? "1000000" : $maxId;
		return $maxId;
	}




}