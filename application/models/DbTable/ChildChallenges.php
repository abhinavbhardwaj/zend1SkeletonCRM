<?php
/*
 * This is a model class for Child Chalanges
 *  
 */
class Application_Model_DbTable_ChildChallenges extends Zend_Db_Table_Abstract
{
	// This is the name of Table
	protected $_name = 'bal_challenges';
	
	public function getAllChalangesData($where){
		$result = $this->fetchAll($where);
		return $result;
	}
	/************function for delete cancel************/
	public function removeChalangesData($rId){
		$where = "request_id = $rId";
		return $this->delete($where);
	}
	
}	