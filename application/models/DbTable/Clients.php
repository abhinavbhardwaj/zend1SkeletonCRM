<?php
/*
 * This is a model class for Product
 * Created By Sunil Khanchandani
 *
 */
class Application_Model_DbTable_Clients extends Zend_Db_Table_Abstract
{
	// This is name of Table
	protected $_name = 'bal_clients';




	//function to add Client
	public function addClient($data)
	{

		$options 		= $this->insert($data);

		return $options;

	}

	/**
	 *Function to get All Client
	 */
	public function getAllClient(){
		$db 				= 		Zend_Db_Table::getDefaultAdapter();
		$select 			= 		$db->select()
									->from(array('clt' => $this->_name), "clt.*")
									->joinLeft(array('ctry' => 'bal_countrycode'),
										'ctry.id = clt.country',
										array("country"=>"ctry.country"))
									->order("clt.created_date desc");
		$clientInfo 		=  		$db->fetchAll($select);
      return $clientInfo;
	}

	/**
	 *Function to get All Active CLient List
	 */
	public function getAllActiveClients(){
		$db 				= 		Zend_Db_Table::getDefaultAdapter();
		$where 				= 		$this->_db->quoteInto("status=?","t");
		$select 			= 		$db->select()
									->from(array('clt' => $this->_name), "clt.*")
									->joinLeft(array('ctry' => 'bal_countrycode'),
										'ctry.id = clt.country',
										array("country"=>"ctry.country"))
									->where($where)
									->order("clt.created_date desc");
 
		$clientInfo 		=  		$db->fetchAll($select);
      return $clientInfo;
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