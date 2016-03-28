<?php
/*
 * This is a model class for Product
 * Created By Sunil Khanchandani
 *
 */
class Application_Model_DbTable_Products extends Zend_Db_Table_Abstract
{
	// This is name of Table
	protected $_name = 'bal_products';




	//function to add categories
	public function addProduct($data)
	{

		$options 		= $this->insert($data);

		return $options;

	}

	/**
	 *Function to get All Product
	 */
	public function getAllProduct(){
		$where	 			= 		"1";
		$order 				= 		"created_date desc";
		return $this->fetchAll($where,$order)->toArray();
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