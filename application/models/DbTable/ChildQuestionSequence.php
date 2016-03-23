<?php
/*
 * This is a model class for Child Subjects Information
 * Created By Suman Khatri
 * Thursday, Sep 03 2013
 */
class Application_Model_DbTable_ChildQuestionSequence extends Zend_Db_Table_Abstract
{
	// This is name of Table
	protected $_name = 'bal_child_question_sequence';
	
	//function to add sequence for child
	public function addSequence($data)
	{
		return $this->insert($data);
	}
	
	//function to remove sequence on basis of child_id
	public function removeSequence($childId)
	{
		$where = $this->_db->quoteInto("child_id = ?",$childId);
		return $this->delete($where);
	}
	
	//function to get sequence on basis of child_id
	public function FetchSequence($childId)
	{
		$where = "child_id = $childId";
		return $this->fetchAll($where);
	}
	
	//function to fetch max sequence number for a particular child
	public function getMaxsequence($childId)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
    	 $select = $db->select()
    	 ->from($this->_name, array(new Zend_Db_Expr('max(sequence_number) as maxsequenceid')))
    	 ->where("child_id='$childId'");
       	$sequenceInfo =  $db->fetchRow($select); 
		return $sequenceInfo;	
	}
	
	//function to fetch max sequence number for a particular child
	public function getMinsequence($childId)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
    	 $select = $db->select()
    	 ->from($this->_name, array(new Zend_Db_Expr('min(sequence_number) as minsequenceid')))
    	 ->where("child_id='$childId'");
       	$sequenceInfo =  $db->fetchRow($select); 
		return $sequenceInfo;	
	}
	
	//function to get next sequence
	public function getNextSequence($sno,$childId)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
    	$select = $db->select()
	    	 ->from($this->_name, array(new Zend_Db_Expr('sequence_number as nextsequenceid')))
	    	 ->where("sequence_number > '$sno' and child_id='$childId'");
	    $select->order('sequence_number');
    	$select->limit(1);
       	$sequenceInfo =  $db->fetchRow($select); 
		return $sequenceInfo;	
	}
	
	//function to remove sequence
	public function DeleteSequence($catId)
	{
		$where = "category_id = $catId";
		return $this->delete($where);
	}
        
        /**
	 * function used to fetch all sequences having question
	 *
	 * @param
	 *        	varchar askedquestion,seq,int childId,gradeId
         * @author Suman Khatri on 3rd April 2014
	 * @return result
	 */
	public function GetAllSeuencesHavingQuestion($childId, $gradId) {
		$db = Zend_Db_Table::getDefaultAdapter ();
		$select = $db->select ()->from ( array (
				'blq' => 'bal_questions' 
		), array (
				'' 
		) )->joinInner ( array (
				'bchs' => 'bal_child_question_sequence' 
		), 'blq.category_id = bchs.category_id', array (
				'bchs.sequence_number' 
		) )
                  ->where ("bchs.child_id = $childId")
                  ->where("blq.grade_id = $gradId OR blq.grade_id = 0")
                  ->where("blq.is_approved = 'Y'")
                  ->group ("bchs.category_id" );
		$allSequesnce = $db->fetchall ( $select );
                return $allSequesnce;
	}
        
        /**
	 * function used to fetch next sequence
	 * @param varchar askedquestion,seq,int childId,gradeId
         * @author Suman Khatri on 3rd April 2014
	 * @return result
	 */
	public function GetNextSeuencesHavingQuestion($sno,$childId,$gradId) {
		$db = Zend_Db_Table::getDefaultAdapter ();
		$select = $db->select ()->from ( array (
				'blq' => 'bal_questions' 
		), array (
				'blq.bal_question_id' 
		) )->joinInner ( array (
				'bchs' => 'bal_child_question_sequence' 
		), 'blq.category_id = bchs.category_id', array (
				'bchs.sequence_number as nextsequenceid' 
		) )
                  ->where ("bchs.child_id = $childId and sequence_number > '$sno'")
                  ->where("blq.grade_id = $gradId OR blq.grade_id = 0")
                  ->where("blq.is_approved = 'Y'")
                  ->group('bchs.sequence_number')
                  ->limit(1);
		$nextSequesnce = $db->fetchRow ( $select );
                return $nextSequesnce;
	}
        
        //function to fetch max sequence number for a particular child
	public function getMinsequenceHavingQuestion($childId,$gradId)
	{
		$db = Zend_Db_Table::getDefaultAdapter ();
		$select = $db->select ()->from ( array (
				'blq' => 'bal_questions' 
		), array (
				'blq.bal_question_id' 
		) )->joinInner ( array (
				'bchs' => 'bal_child_question_sequence' 
		), 'blq.category_id = bchs.category_id', array (
				'min(bchs.sequence_number) as minsequenceid' 
		) )
                  ->where ("bchs.child_id = $childId")
                  ->where("blq.is_approved = 'Y'")
                  ->where("blq.grade_id = $gradId OR blq.grade_id = 0")
                  ->limit(1);
		$nextSequesnce = $db->fetchRow ( $select );
                return $nextSequesnce;
	}
        
        
         //function to fetch max sequence number for a particular child
	public function getMaxsequenceHavingQuestion($childId,$gradId)
	{
		$db = Zend_Db_Table::getDefaultAdapter ();
		$select = $db->select ()->from ( array (
				'blq' => 'bal_questions' 
		), array (
				'blq.bal_question_id' 
		) )->joinInner ( array (
				'bchs' => 'bal_child_question_sequence' 
		), 'blq.category_id = bchs.category_id', array (
				'max(bchs.sequence_number) as maxsequenceid' 
		) )
                  ->where ("bchs.child_id = $childId")
                  ->where("blq.is_approved = 'Y'")
                  ->where("blq.grade_id = $gradId OR blq.grade_id = 0")
                  ->limit(1);
		$nextSequesnce = $db->fetchRow ( $select );
                return $nextSequesnce;
	}
        
	//function to fetch max sequence number for a particular child
	public function getMaxsequenceHavingUnaskedQuestion($childId,$gradId,$askedquestion)
	{
            if($askedquestion != null && !empty($askedquestion)){
                    $where = "blq.bal_question_id not in ($askedquestion)";
                }else{
                    $where = '1';
                }
		$db = Zend_Db_Table::getDefaultAdapter ();
		$select = $db->select ()->from ( array (
				'blq' => 'bal_questions' 
		), array (
				'' 
		) )->joinInner ( array (
				'bchs' => 'bal_child_question_sequence' 
		), 'blq.category_id = bchs.category_id', array (
				'max(sequence_number) as maxsequenceid' 
		) )
                  ->where ("bchs.child_id = $childId")
                  ->where("blq.grade_id = $gradId OR blq.grade_id = 0")
                  ->where("blq.is_approved = 'Y'")
                  ->where($where)
                  ->limit(1);
		$nextSequesnce = $db->fetchRow ( $select );
                return $nextSequesnce;
	}
        
        /**
	 * function used to fetch next sequence
	 * @param varchar askedquestion,seq,int childId,gradeId
         * @author Suman Khatri on 3rd April 2014
	 * @return result
	 */
	public function GetNextSeuencesHavingUnaskedQuestion($sno,$childId,$gradId,$askedquestion) {
                if($askedquestion != null && !empty($askedquestion)){
                    $where = "blq.bal_question_id not in ($askedquestion)";
                }else{
                    $where = '1';
                }
		$db = Zend_Db_Table::getDefaultAdapter ();
		$select = $db->select ()->from ( array (
				'blq' => 'bal_questions' 
		), array (
				'blq.bal_question_id' 
		) )->joinInner ( array (
				'bchs' => 'bal_child_question_sequence' 
		), 'blq.category_id = bchs.category_id', array (
				'sequence_number as nextsequenceid' 
		) )
                  ->where ("bchs.child_id = $childId and sequence_number > '$sno'")
                  ->where("blq.grade_id = $gradId OR blq.grade_id = 0")
                  ->where("blq.is_approved = 'Y'")
                  ->where($where)
                  ->group('bchs.sequence_number')
                  ->limit(1);
		$nextSequesnce = $db->fetchRow ( $select );
                return $nextSequesnce;
	}
        
        //function to fetch max sequence number for a particular child
	public function getMinsequenceHavingUnaskedQuestion($childId,$gradId,$askedquestion)
	{
            if($askedquestion != null && !empty($askedquestion)){
                    $where = "blq.bal_question_id not in ($askedquestion)";
                }else{
                    $where = '1';
                }
		$db = Zend_Db_Table::getDefaultAdapter ();
		$select = $db->select ()->from ( array (
				'blq' => 'bal_questions' 
		), array (
				'blq.bal_question_id' 
		) )->joinInner ( array (
				'bchs' => 'bal_child_question_sequence' 
		), 'blq.category_id = bchs.category_id', array (
				'min(sequence_number) as minsequenceid' 
		) )
                  ->where ("bchs.child_id = $childId")
                  ->where("blq.grade_id = $gradId OR blq.grade_id = 0")
                  ->where("blq.is_approved = 'Y'")
                  ->where($where)
                  ->limit(1);
		$nextSequesnce = $db->fetchRow ( $select );
                return $nextSequesnce;
	}
	
	/**
	 * function used to fetch all sequences having unasked question
	 *
	 * @param
	 *        	varchar askedquestion,seq,int childId,gradeId
         * @author Suman Khatri on 3rd April 2014
	 * @return result
	 */
	public function GetAllSeuencesHavingUnAskedQuestion($childId, $gradId,$askedquestion) {
                if($askedquestion != null && !empty($askedquestion)){
                    $where = "blq.bal_question_id not in ($askedquestion)";
                }else{
                    $where = '1';
                }
		$db = Zend_Db_Table::getDefaultAdapter ();
		$select = $db->select ()->from ( array (
				'blq' => 'bal_questions' 
		), array (
				'' 
		) )->joinInner ( array (
				'bchs' => 'bal_child_question_sequence' 
		), 'blq.category_id = bchs.category_id', array (
				'bchs.sequence_number' 
		) )
                  ->where ("bchs.child_id = $childId")
                  ->where("blq.grade_id = $gradId OR blq.grade_id = 0")
                  ->where("blq.is_approved = 'Y'")
                  ->where($where)
                  ->group ("bchs.category_id" );
		$allSequesnce = $db->fetchall ( $select );
                return $allSequesnce;
	}

}