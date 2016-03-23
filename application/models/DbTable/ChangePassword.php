<?php
/*
 * This is a model class for Change Password
 * Created By Sunil Khanchandani
 * 6 August 2013 
 */
class Application_Model_DbTable_ChangePassword extends Zend_Db_Table_Abstract
{
	// This is name of Table
	protected $_name = 'bal_change_password_request';
	protected $_where;
	

		public function addPassRequest($data)
		{
			return $this->insert($data);
		}
	
	
		public function fetchRequestData($requestCode,$userId)
		{
			$where = $this->_db->quoteInto("verify_code=?", $requestCode);
			$where .= $this->_db->quoteInto(" AND user_id=?", $userId);
			return $this->fetchRow($where);
		}
	
	
		public function updateRequestData($requestData,$userId,$requestCode)
		{
			$where = $this->_db->quoteInto("user_id=?", $userId); 
			$where .= $this->_db->quoteInto(" AND verify_code=?", $requestCode); 
			return $this->update($requestData, $where);
		}
                
                /**
                * Function to get previous unused change password request
                * 
                * @param
                *        	array
                * @author Suman Khatri on 20 August 2014
                * @return ArrayIterator
                */
                public function getAllPreviousRequest($userId,$createdDate)
		{
			$where = $this->_db->quoteInto("used=?", 'N');
			$where .= $this->_db->quoteInto(" AND user_id=?", $userId);
                        $where .= $this->_db->quoteInto(" AND created_date !=?", $createdDate);
			return $this->fetchAll($where);
		}
                
                /**
                * Function to expire previous unused change password request
                * 
                * @param
                *        	array
                * @author Suman Khatri on 20 August 2014
                * @return ArrayIterator
                */
                public function expirePreviousRequests($requestData,$requestId)
		{
			$where = $this->_db->quoteInto("request_id=?", $requestId); 
			return $this->update($requestData, $where);
		}



}// end of class Application_Model_DbTable_ChangePassword