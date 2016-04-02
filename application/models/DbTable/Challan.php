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
	public function getChallanById($challanId){
		$db 				= 		Zend_Db_Table::getDefaultAdapter();
		$select 			= 		$db->select()
									->from(array('ch' => $this->_name), "ch.*")									
									->joinLeft(array('op' => 'bal_ordered_product'),
										'ch.order_product_id = op.id',
										array( 
											  "given_quentity"=>"op.given_quentity"
											  )
										)
									->joinLeft(array('pro' => 'bal_products'),
										'pro.id = ch.product_id',
										array("product_name"=>"pro.name",
											  "unit"=>"pro.unit",
											  "rate"=>"pro.price"
											  )
										)
									->joinLeft(array('cli' => 'bal_clients'),
										'cli.id = ch.client_id',
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
									->where("ch.id =  $challanId"); 
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




}