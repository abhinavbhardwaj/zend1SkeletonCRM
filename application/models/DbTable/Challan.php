<?php
/*
 * This is a model class for Purchase Order
 * Created By Abhinav Bhardwaj
 *
 */
class Application_Model_DbTable_Challan extends Zend_Db_Table_Abstract
{
	// This is name of Table
	protected $_name = 'bal_challan';




	//function to add categories
	public function addChallan($data)
	{

		$insertId 		= $this->insert($data);
		return $insertId;

	}

	/**
	 *Function to Purchase Order by order Id
	 */
	public function getpurchaseOrderById($orderId){
		$db 				= 		Zend_Db_Table::getDefaultAdapter();
		$select 			= 		$db->select()
									->from(array('po' => $this->_name), "po.*")
									->joinLeft(array('op' => 'bal_ordered_product'),
										'op.po_id = po.id',
										array("ordered_quentity"=>"op.ordered_quentity",
											  "given_quentity"=>"op.given_quentity",
											  "rate"=>"op.rate",
											  "amount"=>"op.amount",
											  "remark"=>"op.remark"
											  )
										)
									->joinLeft(array('pro' => 'bal_products'),
										'pro.id = op.product_id',
										array("product_name"=>"pro.name",
											  "unit"=>"pro.unit"
											  )
										)
									->joinLeft(array('cli' => 'bal_clients'),
										'cli.id = po.client_id',
										array("client_address"=>"cli.address",
											  "client_company_name"=>"cli.company_name",
											  "client_name"=>"cli.name",
											  "client_phone"=>"cli.phone",
											  "client_city"=>"cli.city",
											  "client_state"=>"cli.state",
											  "client_country"=>"cli.country",
											  "client_zip"=>"cli.zip"
											  )
										)
									->where("po.id =  $orderId");
		$poInfo 			=  		$db->fetchAll($select);
      return $poInfo;
	}

	/**
	 *Function to get challan number
	 *Here we are just taking higest chalan id from challan table
	 */
	public function getChallanNo(){
		$db 				= 		Zend_Db_Table::getDefaultAdapter();
		$select 			= 		$db->select()->from($this->_name, array(new Zend_Db_Expr("MAX(id) AS maxID")));

		$maxId				=		((int)$db->fetchOne($select)+1);
		 
		return $maxId;
	}

	//function to update categories Data
	public function updatetrophyData($trophyDataArray,$trophy_id)
	{
		$where = $this->_db->quoteInto("trophy_id 	=?",$trophy_id);
		$updateCatData 		= $this->update($trophyDataArray,$where);
		return $updateCatData;
	}

	public function check_title($title){

		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$where = $this->_db->quoteInto("title=?",$title);
		$select = $db->select()
		->from($this->_name, array('count(*) as tot'))
		->where($where);
		$categoryData = $db->fetchRow($select);

		return $categoryData['tot'];

	}



}