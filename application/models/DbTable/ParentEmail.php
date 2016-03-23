<?php
/*
 * This is a model class for Parent Email
 * Created By Sunil Khanchandani
 * Thursday, July 15 2013 
 */
class Application_Model_DbTable_ParentEmail extends Zend_Db_Table_Abstract
{
	// This is name of Table
	protected $_name = 'bal_parent_emails';
	protected  $_primary = 'email_id';
	
	public function addEmailVerifyData($data)
	{
		
		return $this->insert($data);
	}
	
	/*
	 * This is a function to fetch all email address of a particulat parent
	 * @param Parent Id
	 */
	
	public function getAllEmail($parentId)
	{
		$where= $this->_db->quoteInto("parent_id =?",$parentId);
		$getEmailId = $this->fetchAll($where); 
		return $getEmailId->toArray();
	}
	
	/*
	 * This is a function to remove email
	 */
	
public function removeEmailData($emailId)
	{
		$where = $this->_db->quoteInto("email_id=?", $emailId);
		return $this->delete($where);
	}
	
	/*
	 * This is a function to fetch the data of parent email
	 */
	public function fetchEmailData($emailId,$fetchDataArray)
	{
		
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$where=  $db->quoteInto("email_id=?",$emailId);
		$select = $db->select()
					 ->from($this->_name,$fetchDataArray)
					 ->where($where); 
		$fetchParentEmailData = $db->fetchRow($select); 
		return $fetchParentEmailData;		
	}
	
	
public function verifyOtherMail($parId,$verficationCode,$emaiId)
	{
		$where	= $this->_db->quoteInto("parent_id=?",$parId);
		$where	.= $this->_db->quoteInto(" AND email_id=?",$emaiId);
		$where .= $this->_db->quoteInto(" AND verification_code = ?",$verficationCode);
		 $parExist = $this->fetchRow($where);
		 if($parExist){
		 	$statusData = array('verified' =>'Y');
		 	$updateStatus = $this->update($statusData, $where);
		 	if($updateStatus){
		 		return true;
		 	}else{
		 		return false;
		 	}
		 }
	}
	
	//this function is used to add emails
	public function addEmail($data)
	{
		return $this->insert($data);
	}
	
	//check existance of email in DB
	public function isExistsEmail($email)
	{
		$where	= "email = '$email'";
		$parExist = $this->fetchRow($where);
		if($parExist) 
		{
			return true;
		} 
		else 
		{
			return false;
		}
	}
	
	
public function verifyParentMail($parId,$verficationCode)
	{
		$where	= $this->_db->quoteInto("parent_id=?",$parId);
		$where .= $this->_db->quoteInto(" AND verification_code =?",$verficationCode);
		 $parExist = $this->fetchRow($where); 
		 if($parExist){
		 	$statusData = array('verified' =>'Y');
		 	$updateStatus = $this->update($statusData, $where);
		 	if($updateStatus){
		 		return true;
		 	}else{
		 		return false;
		 	}
		 }
	}
	/**
	 * function to update parent email 
	 * @param varchar $parId,$updateEmail
	 * @return lat update record
	 * @author Suman khatri on 25 Nov. 2013
	 */	
	public function updateParentEmail($parId,$updateEmail) {
		$where	= $this->_db->quoteInto("parent_id=?",$parId);
		$updateStatus = $this->update($updateEmail, $where);
		return $updateStatus;
	}
}	