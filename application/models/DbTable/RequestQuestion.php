<?php
/*
 * This is a model class for request question information
 * Created By suman khatri
 * Thursday, July 29 2013 
 */
class Application_Model_DbTable_RequestQuestion extends Zend_Db_Table_Abstract
{
	// This is name of Table
	protected $_name = 'bal_child_questions';
	
	public function getRequestQuestion($requestId)
	{
		$where 			= "blqr.request_id = $requestId";
		/*$getquestion 	= $this->fetchRow($where);
		return $getquestion;*/
		$db = Zend_Db_Table::getDefaultAdapter();
		  $select = $db->select()
		->from(array('blq' => 'bal_child_questions'),
		array('blq.*'))
		->joinLeft(array('blqr' => 'bal_child_question_requests'),
					'blq.request_id = blqr.request_id',     	 				
		array('longitude','latitude'))
		->where("blqr.response_date != '' ")
		->where($where)
		->order('blqr.request_id DESC');
		
		$getquestion =  $db->fetchRow($select);
		return $getquestion;
	}
	
	//function to add question on basis of request and child device info
	public function AddQuestion($data)
	{
		return $this->insert($data);
	}
	
	//public function to get question
	public function GetQuestionId($rId)
	{
		$where = "request_id = $rId";
		return $this->fetchRow($where);		
	}
	
	/*function for 
	 * get last responded question by child
	 * @param int child id , int grade id
	 * @retrun array
	 * created by suman on 23rd sep 2013 
	 */
	public function getLastRespondedQuestion($childId,$gradeId)
	{
		$where 			= "blqr.child_id = $childId and blqr.grade_id = $gradeId";
		$db = Zend_Db_Table::getDefaultAdapter();
		  $select = $db->select()
		->from(array('blq' => 'bal_child_questions'),
		array('blq.*'))
		->joinLeft(array('blqr' => 'bal_child_question_requests'),
					'blq.request_id = blqr.request_id',     	 				
		array('longitude','latitude'))
		->where("blqr.response_date != '' ")
		->where($where)
		->order('blqr.request_id DESC');
		$getquestion =  $db->fetchRow($select);
		return $getquestion;
	}
	
	/*function for 
	 * update responce of question given by child
	 * @param int request id , data
	 * @retrun result
	 * created by suman on 29th nov 2013 
	 */
	public function updatQuestionResponce($data,$rId)
	{
		$where = "request_id = $rId";
		return $this->update($data, $where);
	}
	
	
	/*function for
	 * update responce of question given by child
	* @param int request id , data
	* @retrun result
	* created by suman on 29th nov 2013
	*/
	public function removeQuestionResponce($rId)
	{
		$where = "request_id = $rId";
		return $this->delete($where);
	}
	/***************
	 * function for delete all child questions
	* @param deviceId Int
	* return int
	*
	* *************************/
	public function deleteData($childDeviceId)
	{
		$where = $this->_db->quoteInto("child_device_id = ?",$childDeviceId);
		return $this->delete($where);
	}
	
}	