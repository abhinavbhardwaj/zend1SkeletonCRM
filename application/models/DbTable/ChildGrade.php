<?php
/*
 * This is a model class for Child Information
 * Created By Sunil Khanchandani
 * Thursday, July 15 2013 
 */
class Application_Model_DbTable_ChildGrade extends Zend_Db_Table_Abstract
{
	// This is name of Table
	protected $_name = 'bal_grades';
	
	
	/*
	 * This is a function to check the child exist for parent or not
	 */
	
	public function getSubjectListOnGrade($gradeId){
		
	
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$where =  $db->quoteInto("subject_id IN (SELECT subject_id from bal_subject_grades Where grade_id = ?)",$gradeId); 
		$select = $db->select()
					 ->from('bal_subjects',array('subject_id','subject_name'))
					 ->where($where);
		
		$subjectList = $db->fetchAll($select); 
		return $subjectList; 			 
		
	}
	
	
	public function addChildSubjectInfo($data){
		return $this->insert($data);
	}
	
	public function getAllGradeList(){

		$gradeList = $this->fetchAll(); 
		return $gradeList->toArray(); 			 
		
	}
	
	
	public function checkGradeExist($gradeName)
	{
		$where = "grade_name = '$gradeName'"; 
		$gradeExist = $this->fetchRow($where);
		if($gradeExist){
			return true;
		}else{
			return false;
		}		
	}
	
	public function getGradeDataOnGradeName($gradeName,$fetchDataArray){
		
	
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$where =  $db->quoteInto("grade_name = ?",$gradeName); 
		$select = $db->select()
					 ->from('bal_grades',$fetchDataArray)
					 ->where($where);
		
		$gradeData = $db->fetchRow($select); 
		return $gradeData; 			 
		
	}
	
	public function getGrade($where)
	{
		return $this->fetchRow($where);
	}
}	