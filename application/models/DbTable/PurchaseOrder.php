<?php
/*
 * This is a model class for Purchase Order
 * Created By Abhinav Bhardwaj
 *
 */
class Application_Model_DbTable_PurchaseOrder extends Zend_Db_Table_Abstract
{
	// This is name of Table
	protected $_name = 'bal_purchase_order';




	//function to add categories
	public function addPurchaseOrder($data)
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
										array(
											 "order_product_id"=>"op.id",
											  "ordered_quentity"=>"op.ordered_quentity",
											  "given_quentity"=>"op.given_quentity",
											  "rate"=>"op.rate",
											  "amount"=>"op.amount",
											  "remark"=>"op.remark"
											  )
										)
									->joinLeft(array('pro' => 'bal_products'),
										'pro.id = op.product_id',
										array("product_id"=>"pro.id",
											  "product_name"=>"pro.name",
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
	 *Function to get All Product
	 */
	public function getAllPurchaseOrder(){
		$db 				= 		Zend_Db_Table::getDefaultAdapter();
		$select 			= 		$db->select()
									->from(array('po' => $this->_name),
										   array("id"=>"po.id",
											  "status"=>"po.status"
											  )
										   )
									->joinLeft(array('op' => 'bal_ordered_product'),
										'op.po_id = po.id',
										array("ordered_quentity"=>"op.ordered_quentity",
											  "amount"=>"op.amount"
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
										array(
											  "client_name"=>"cli.name"
											  )
										)
									->order("po.created_date desc");
		$poInfo 			=  		$db->fetchAll($select);
		return $poInfo;
	}

	//function to update   Data
	public function updateData($dataArray,$id)
	{
		$where = $this->_db->quoteInto("id 	=?", $id);
		$updateCatData 		= $this->update($dataArray,$where);
		return $updateCatData;
	}



}