<?php
/*
 * This is a model class for Child Goals
 * Created By Sunil Khanchandani
 * Thursday, July 15 2013 
 */
class Application_Model_DbTable_ChildGoals extends Zend_Db_Table_Abstract
{
	// This is name of Table
	protected $_name = 'bal_child_goals';
	
	
	/*
	 * This is a function to add the child goals
	 */
	
	
	public function addChildGoals($data){
		return $this->insert($data);
	}
	
        /**
	 * Function to get child goals on the basis of child_id and learningUpdate
	 * @param $childId,$learningUpdate
	 * @author updated by Suman Khatri on 9th October 2014
	 * @return array
	 */
	//this function is used to fetch child goals on the basis of child_id
	public function fetchGoals($childId,$learningUpdate = null)
	{
                $where = $this->_db->quoteInto("child_id = ?",$childId); 
                if($learningUpdate != null || !empty($learningUpdate)){
                    $where .= $this->_db->quoteInto(" and learnig_updated = ?",$learningUpdate);
                }
		$goals = $this->fetchRow($where);
		return $goals;
		
	}
	
	//function to fetch child goals
	public function getChildGoals($childId)
	{
		$where = "child_id = $childId";
		return $this->fetchRow($where);
	}
	
	/*
	 * This is a function to update child Goals Info
	 */
	public function updateChildGoals($data,$childId)
	{
		$where = $this->_db->quoteInto("child_id = ?",$childId);
		return $this->update($data,$where);
	}
	
//check existance of email in DB
	public function isExistsGoals($chilId)
	{
		$where	= $this->_db->quoteInto("child_id = ?",$chilId);
		$goalsExist = $this->fetchRow($where);
		if($goalsExist) 
		{
			return true;
		} 
		else 
		{
			return false;
		}
	}
	/***************
	 * function for child goals
	* @param childId Int
	* return int
	*
	* *************************/
	public function deleteData($childId)
	{
		$where = $this->_db->quoteInto("child_id = ?",$childId);
		return $this->delete($where);
	}
}	