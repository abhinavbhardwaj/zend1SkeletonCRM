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
	 *Function to Purchase Order by order Id
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