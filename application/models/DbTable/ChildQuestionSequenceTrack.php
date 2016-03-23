<?php
/*
 * This is a model class for Child Subjects Information
 * Created By Suman Khatri
 * Thursday, Sep 03 2013
 */
class Application_Model_DbTable_ChildQuestionSequenceTrack extends Zend_Db_Table_Abstract
{
	// This is name of Table
	protected $_name = 'bal_child_question_sequence_track';
	
	//function to add sequence for child
	public function addSequenceTrack($data)
	{
		return $this->insert($data);
	}
	
	//function to get current sequence number
	public function getCurrentsequencenumber($childId)
	{
		$where = "child_id =  $childId";
		return $this->fetchRow($where);
	}
	
	//function to update current sequence number
	public function updateData($data,$childId)
	{
		$where = "child_id =  $childId";
		return $this->update($data, $where);
	}
	/***************
	 * function for delete child question sequence
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