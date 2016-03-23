<?php
/*
 * This is a model class for Child Subjects Information Created By Sunil Khanchandani Thursday, July 15 2013
 */
class Application_Model_DbTable_ChildQuestion extends Zend_Db_Table_Abstract {
	// This is name of Table
	protected $_name = 'bal_questions';
	
	/**
	 * function to fetch question list according to subject id's and grade id
	 *
	 * @param
	 *        	int gradeId,subjectId
	 * @return Array List
	 */
	public function getChildQuestion($gradeId, $subjectId) {
		$db = Zend_Db_Table_Abstract::getDefaultAdapter ();
		$where = $db->quoteInto ( "grade_id=?", $gradeId );
		$select = $db->select ()->from ( $this->_name, 'device_key' )->where ( $where );
		$getDeviceKey = $db->fetchRow ( $select );
		return $getDeviceKey;
	}
	
	/**
	 * function to get questioninfo
	 *
	 * @param
	 *        	int questionId
	 * @return Array
	 */
	public function getQuestion($questionId) {
		$where = "bal_question_id 	 = $questionId";
		$question = $this->fetchRow ( $where );
		return $question;
	}
	
	/**
	 * function used to set question for child
	 *
	 * @param
	 *        	int gradeId,subjectId
	 * @return Array
	 */
	public function setQuestionTochild($subjectId, $gradId) {
		$where = "grade_id = $gradId and subject_id in($subjectId)";
		/*
		 * $limit = "limit 1"; $question = $this->fetchAll($where ,null,$limit); print_r($question);die;
		 */
		
		$db = Zend_Db_Table::getDefaultAdapter ();
		$select = $db->select ()->from ( array (
				'blq' => 'bal_questions' 
		), array (
				'blq.*' 
		) )->joinLeft ( array (
				'blo' => 'bal_question_options' 
		), 'blq.bal_question_id = blo.question_id' )->where ( $where )->where ( "blo.answer = 'Y'" );
		$select->order ( new Zend_Db_Expr ( 'RAND()' ) );
		$select->limit ( 1 );
		$question = $db->fetchRow ( $select );
		return $question;
	}
	
	/**
	 * function used to add question
	 *
	 * @param
	 *        	array(data)
	 * @return last insert id
	 */
	public function addQuestion($data) {
		$question = $this->insert ( $data );
		return $question;
	}
	
	/**
	 * function used to get question list
	 *
	 * @param
	 *        	int categoryId
	 * @return Array List
	 */
	public function getQuestions($categoryId) {
		if ($categoryId == '') {
			$db = Zend_Db_Table::getDefaultAdapter ();
			$select = $db->select ()->from ( array (
					'blq' => 'bal_questions' 
			), array (
					'blq.bal_question_id',
					'blq.question',
					'blq.modified_Date',
					'blq.created_date',
					'blq.created_by',
					'blq.modified_by' 
			) )->joinLeft ( array (
					'blc' => 'bal_question_categories' 
			), 'blq.category_id = blc.category_id', array (
					'blc.category_id',
					'blc.category_code' 
			) )->joinLeft ( array (
					'blg' => 'bal_grades' 
			), 'blq.grade_id = blg.grades_id', array (
					'blg.grades_id',
					'blg.grade_name' 
			) )->where ( "blq.category_id = ''" );
			$question = $db->fetchall ( $select );
		} else {
			$db = Zend_Db_Table::getDefaultAdapter ();
			$select = $db->select ()->from ( array (
					'blq' => 'bal_questions' 
			), array (
					'blq.bal_question_id',
					'blq.question',
					'blq.modified_Date',
					'blq.created_date',
					'blq.created_by',
					'blq.modified_by' 
			) )->joinLeft ( array (
					'blc' => 'bal_question_categories' 
			), 'blq.category_id = blc.category_id', array (
					'blc.category_id',
					'blc.category_code' 
			) )->joinLeft ( array (
					'blg' => 'bal_grades' 
			), 'blq.grade_id = blg.grades_id', array (
					'blg.grades_id',
					'blg.grade_name' 
			) )->where ( "blq.category_id in ($categoryId)" );
			$question = $db->fetchall ( $select );
		}
		return $question;
	}
	
	/**
	 * function used to get question using search functionality
	 *
	 * @param
	 *        	varchar searchfield
	 * @return Array List
	 */
	public function getQuestionsbysearch($search_field) {
		$todayDate 					= date("Y-m-d");//date return today server date and time.
		$where1 = "(blq.expiry_date > '$todayDate' or blq.expiry_date is null) and blq.is_approved = 'Y'";
		$where2 =  $this->_db->quoteInto("blq.question like?","%$search_field%");
		$db = Zend_Db_Table::getDefaultAdapter ();
		$select = $db->select ()->from ( array (
				'blq' => 'bal_questions' 
		), array (
				'blq.bal_question_id',
				'blq.question',
				'blq.question_equation_image_name',
				'blq.modified_Date',
				'blq.created_date',
				'blq.approved_date',
				'blq.approved_by',
				'blq.created_by',
				'blq.modified_by',
				'blq.question_display',
				'blq.question_equation_images'
		) )->joinLeft ( array (
				'blc' => 'bal_question_categories' 
		), 'blq.category_id = blc.category_id', array (
				'blc.category_id',
				'blc.category_code' 
		) )->joinLeft ( array (
				'blg' => 'bal_grades' 
		), 'blq.grade_id = blg.grades_id', array (
				'blg.grades_id',
				'blg.grade_name' 
		) )->where ($where2)->where($where1)->order ("blq.date desc");
		$question = $db->fetchall ( $select );
		return $question;
	}
	
	/**
	 * function used to update question
	 *
	 * @param
	 *        	array(data),where condition
	 * @return no. of affected rows
	 */
	public function updateQuestion($dataqUpdate, $wherequestion) {
		return $this->update ( $dataqUpdate, $wherequestion );
	}
	
	/**
	 * function used to delete question
	 *
	 * @param
	 *        	where condition
	 * @return no. of affected rows
	 */
	public function deleteQuestion($where) {
		return $this->delete ( $where );
	}
	
	/**
	 * function used to get question info
	 *
	 * @param
	 *        	int id
	 * @return Array
	 */
	public function getQuestionsbyid($id) {
		$db = Zend_Db_Table::getDefaultAdapter ();
		$select = $db->select ()->from ( array (
				'blq' => 'bal_questions' 
		), array (
				'blq.*' 
		) )->joinLeft ( array (
				'blc' => 'bal_question_categories' 
		), 'blq.category_id = blc.category_id', array (
				'blc.category_id',
				'blc.category_code' 
		) )->joinLeft ( array (
				'blg' => 'bal_grades' 
		), 'blq.grade_id = blg.grades_id', array (
				'blg.grades_id',
				'blg.grade_name' 
		) )->where ( "blq.bal_question_id = '$id'" );
		$question = $db->fetchrow ( $select );
		return $question;
	}
	public function getQuestionsForCsv() {
		$db = Zend_Db_Table::getDefaultAdapter ();
		$select = $db->select ()->from ( array (
				'blq' => 'bal_questions'
		), array (
				'blq.question',
				'blq.bal_question_id',
				'difficulty_level',
				'blq.grade_id',
				'blq.category_id',
				'blq.explanation',
				'blq.set_question',
				'blq.refer_book_name',
				'blq.refer_book_chapter',
				'blq.refer_article_url',
				'blq.wolframalphaquery'
		) )->joinLeft ( array (
				'blc' => 'bal_question_categories'
		), 'blq.category_id = blc.category_id', array (
				'blc.category_id',
				'blc.category_code',
				'blc.subject_id'
		) )->joinLeft ( array (
				'bls' => 'bal_subjects'
		), 'bls.subject_id = blc.subject_id', array (
				'bls.subject_name',
		) )->joinLeft ( array (
				'blg' => 'bal_grades'
		), 'blq.grade_id = blg.grades_id', array (
				'blg.grades_id',
				'blg.grade_name'
		) )->where("blq.is_approved='Y' AND blq.category_id <> 0 AND bls.subject_name <> 'math.content'")
                       ->order('blg.grade_name');
                $question = $db->fetchAll( $select );
		return $question;
	}	
	
	
	/**
	 * function used to get question count
	 *
	 * @param
	 *        	int categoryId
	 * @return int count
	 */
	public function getCategoryQuesCount($categoryId) {
		$where = $this->_db->quoteInto ( "category_id=?", $categoryId );
		$questionCount = count ( $this->fetchAll ( $where ) );
		return $questionCount;
	}
	
	/**
	 * function used to fetch question according to sequence number
	 *
	 * @param
	 *        	varchar seqnum,askedquestion,level,int childId,gradeId,subjectId
	 * @return Array
	 */
	public function GetQuestionbySequenChallenge($childId, $askedquestion, $gradId, $subjectId, $level, $domainId) {
        
            $db = Zend_Db_Table::getDefaultAdapter();
            $select = $db->select();
            $select->from(array('blq' => 'bal_questions'), array(
                'blq.bal_question_id', 'blq.question', 'blq.question_equation_image_name',
                'blq.question_display', 'blq.question_equation_images'
            ));
            $select->joinLeft(array('blc' => 'bal_question_categories'), 'blq.category_id = blc.category_id', array(
                'blc.category_id', 'blc.category_code'
            ));
            
            $select->joinLeft(array('blo' => 'bal_question_options'), 'blq.bal_question_id = blo.question_id', array(
                'question_option_id', 'blo.option',
                'blo.option_equation_image_name', 'blo.option_equation'
            ));

            $select->joinLeft(array('bchs' => 'bal_child_question_sequence'), 'blq.category_id = bchs.category_id', array ('bchs.category_id'));
            $select->where("bchs.child_id = ?", $childId);
            
            $select->where("(blc.subject_id = 23 and blq.expiry_date >= ?) or (blc.subject_id != 23)", date('Y-m-d'));

            if ($askedquestion != '') {
                $select->where("blq.bal_question_id not in ($askedquestion)");
            }
            
            $select->where("blq.grade_id = ? OR blq.grade_id = 0", $gradId);
            $select->where("blq.difficulty_level = ?", $level);
            $select->where("blc.subject_id = ?", $subjectId);

            if (!empty($domainId)) {
                $select->where("blc.domain_id = ?", $domainId);
            }

            $select->where("blq.is_approved = 'Y'");
            $select->group("blq.bal_question_id");
            $select->limit(1);
            $question = $db->fetchRow($select);

            return $question;
        }

    /**
	 * function used to fetch question according to sequence number
	 *
	 * @param
	 *        	varchar seqnum,askedquestion,int childId,gradeId
	 * @return Array
	 */
	public function GetQuestionbysequence($seqnum, $askedquestion, $childId, $gradId) {
		$date = date('Y-m-d');
		$where = "(blc.subject_id = 23 and blq.expiry_date >= '$date') or (blc.subject_id != 23)";
		if ($askedquestion != '') {
			
			$db = Zend_Db_Table::getDefaultAdapter ();
			$select = $db->select ()->from ( array (
					'blq' => 'bal_questions' 
			), array (
					'blq.*' 
			) )->joinLeft ( array (
					'blc' => 'bal_question_categories' 
			), 'blq.category_id = blc.category_id', array (
					'blc.category_id',
					'blc.category_code' 
			) )->joinLeft ( array (
					'bchs' => 'bal_child_question_sequence' 
			), 'blq.category_id = bchs.category_id', array (
					'bchs.category_id' 
			) )->joinLeft ( array (
					'blo' => 'bal_question_options' 
			), 'blq.bal_question_id = blo.question_id' )->where ( "blq.bal_question_id not in ($askedquestion)" )->where("blq.is_approved = 'Y'")->where($where)->where ( "bchs.sequence_number = '$seqnum'" )->where ( "bchs.child_id = $childId" )->where ( "blq.grade_id = '$gradId' OR blq.grade_id = 0" )->group ( "blq.bal_question_id" )->limit ( 1 );
			$question = $db->fetchrow ( $select );
			return $question;
		} else {
			$db = Zend_Db_Table::getDefaultAdapter ();
			$select = $db->select ()->from ( array (
					'blq' => 'bal_questions' 
			), array (
					'blq.*' 
			) )->joinLeft ( array (
					'blc' => 'bal_question_categories' 
			), 'blq.category_id = blc.category_id', array (
					'blc.category_id',
					'blc.category_code' 
			) )->joinLeft ( array (
					'bchs' => 'bal_child_question_sequence' 
			), 'blq.category_id = bchs.category_id', array (
					'bchs.category_id' 
			) )->joinLeft ( array (
					'blo' => 'bal_question_options' 
			), 'blq.bal_question_id = blo.question_id' )->where ( "bchs.sequence_number = '$seqnum'" )->where($where)->where ( "bchs.child_id = $childId" )->where ( "blq.grade_id = '$gradId' OR blq.grade_id = 0" )->group ( "blq.bal_question_id" )->limit ( 1 );
			$question = $db->fetchrow ( $select );
			return $question;
		}
	}
	
	/**
	 * function used to fetch question
	 *
	 * @param
	 *        	varchar askedquestion,int childId,gradeId,currentSequence
	 * @return ArrayIterator
	 */
	public function GetExistQuestion($currentSequence, $askedquestion, $childId, $gradId) {
		$date = date('Y-m-d');
		if ($askedquestion != '') {
			$where = "bchs.sequence_number = '$currentSequence' and blq.bal_question_id not in ($askedquestion)";
		} else {
			$where = "bchs.sequence_number = '$currentSequence'";
		}
		
		$where1 = "(blc.subject_id = 23 and blq.expiry_date >= '$date') or (blc.subject_id != 23)";
		
		$db = Zend_Db_Table::getDefaultAdapter ();
		$select = $db->select ()->from ( array (
				'blq' => 'bal_questions' 
		), array (
				'blq.*' 
		) )->joinInner ( array (
				'bchs' => 'bal_child_question_sequence' 
		), 'blq.category_id = bchs.category_id', array (
				'bchs.category_id' 
		) )
		->joinInner ( array (
				'blc' => 'bal_question_categories' 
		), 'blc.category_id = bchs.category_id', array (
				'blc.subject_id' 
		) )->where ( $where )->where ( "bchs.child_id = $childId" )->where($where1)->where("blq.is_approved = 'Y'")->where ( "blq.grade_id = '$gradId' OR blq.grade_id = 0" )->group ( "blq.bal_question_id" )->limit(1);
                
                $select->order(new Zend_Db_Expr('RAND()'));
                
                $question = $db->fetchall ( $select );
		
		/*
		 * $db = Zend_Db_Table::getDefaultAdapter(); $select = $db->select() ->from($this->_name) ->where("category_id = '$currentSequence'") ->where("bal_question_id not in ($askedquestion)"); echo $select;die; $questionInfo = $db->fetchAll($select);
		 */
		return $question;
	}
	
	/**
	 * ****************function for challenges*****************************
	 */
	public function GetExistQuestionChallenge($currentSequence, $askedquestion, $childId, $gradId, $subjectId, $level) {
		$date = date('Y-m-d');
		$where1 = "(blc.subject_id = 23 and blq.expiry_date >= '$date') or (blc.subject_id != 23)";
		if ($askedquestion != '') {
			$where = "bchs.sequence_number = '$currentSequence' and blq.bal_question_id not in ($askedquestion)";
		} else {
			$where = "bchs.sequence_number = '$currentSequence'";
		}
		
		$db = Zend_Db_Table::getDefaultAdapter ();
		$select = $db->select ()->from ( array (
				'blq' => 'bal_questions' 
		), array (
				'blq.*' 
		) )->joinLeft ( array (
				'bchs' => 'bal_child_question_sequence' 
		), 'blq.category_id = bchs.category_id', array (
				'bchs.category_id' 
		) )->joinLeft ( array (
				'blc' => 'bal_question_categories' 
		), 'blq.category_id = blc.category_id', array (
				'blc.category_id',
				'blc.category_code' 
		) )->where ( $where )
		->where($where1)
		->where ( "bchs.child_id = $childId" )
		->where ( "blq.grade_id = '$gradId' OR blq.grade_id = 0" )
		->group ( "blq.bal_question_id" )
		->where ( "blq.difficulty_level = '$level'" )
		->where("blq.is_approved = 'Y'")
		->where ( "blc.subject_id = '$subjectId'" );
		$select->limit(1);
		$question = $db->fetchRow( $select );
		
		/*
		 * $db = Zend_Db_Table::getDefaultAdapter(); $select = $db->select() ->from($this->_name) ->where("category_id = '$currentSequence'") ->where("bal_question_id not in ($askedquestion)"); echo $select;die; $questionInfo = $db->fetchAll($select);
		 */
		return $question;
	}
	
	/**
	 * function used to fetch question
	 *
	 * @param
	 *        	varchar askedquestion,seq,int childId,gradeId
	 * @return ArrayIterator
	 */
	public function GetExistQuestionforallsequence($seq, $askedquestion, $childId, $gradId) {
		$date = date('Y-m-d');
		if ($askedquestion != '') {
			$where = "blq.bal_question_id not in ($askedquestion) and bchs.sequence_number in ($seq)";
		} else {
			$where = "bchs.sequence_number in ($seq)";
		}
		$where1 = "(blc.subject_id = 23 and blq.expiry_date >= '$date') or (blc.subject_id != 23)";
		$db = Zend_Db_Table::getDefaultAdapter ();
		$select = $db->select ()->from ( array (
				'blq' => 'bal_questions' 
		), array (
				'blq.*' 
		) )->joinInner ( array (
				'bchs' => 'bal_child_question_sequence' 
		), 'blq.category_id = bchs.category_id', array (
				'bchs.category_id' 
		) )->joinInner ( array (
				'blc' => 'bal_question_categories' 
		), 'blc.category_id = bchs.category_id', array (
				'blc.subject_id' 
		) )->where ( $where )->where ( "bchs.child_id = $childId" )->where("blq.is_approved = 'Y'")->where($where1)->where ( "blq.grade_id = '$gradId' OR blq.grade_id = 0" )->group ( "blq.bal_question_id" );
		$question = $db->fetchall ( $select );
		return $question;
	}
	public function GetExistQuestionforallsequenceChallenge($seq, $askedquestion, $childId, $gradId, $subjectId, $level,$domainId) {
		$date = date('Y-m-d');
		$where1 = "(blc.subject_id = 23 and blq.expiry_date >= '$date') or (blc.subject_id != 23)";
		if ($askedquestion != '') {
			$where = "blq.bal_question_id not in ($askedquestion) and bchs.sequence_number in ($seq)";
		} else {
			$where = "bchs.sequence_number in ($seq)";
		}
                if(!empty($domainId) && $domainId != 0){
                    $whereDomain = "blc.domain_id = '$domainId'";
                }else{
                    $whereDomain = 1;
                } 
		$db = Zend_Db_Table::getDefaultAdapter ();
		$select = $db->select ()->from ( array (
				'blq' => 'bal_questions' 
		), array (
				'blq.*' 
		) )->joinLeft ( array (
				'bchs' => 'bal_child_question_sequence' 
		), 'blq.category_id = bchs.category_id', array (
				'bchs.category_id' 
		) )->joinLeft ( array (
				'blc' => 'bal_question_categories' 
		), 'blq.category_id = blc.category_id', array (
				'blc.category_id',
				'blc.category_code' 
		) )->where ( $where )
		->where($where1)
		->where ( "bchs.child_id = $childId" )
		->where ( "blq.grade_id = '$gradId' OR blq.grade_id = 0" )
		->where ( "blq.difficulty_level = '$level'" )
                ->where ( "blc.subject_id = '$subjectId'" )
		->where ($whereDomain)
		->where ("blq.is_approved = 'Y'")
		->group ( "blq.bal_question_id" );
		$question = $db->fetchAll ( $select );
		return $question;
	}
	/**
	 * function used to fetch question randomly
	 *
	 * @param
	 *        	varchar askedquestion,int childId,gradeId,currentSequence
	 * @return Array
	 */
	public function GetQuestionRandomly($currentSequence, $childId, $gradId) {
		$date = date('Y-m-d');
		$where = "(blc.subject_id = 23 and blq.expiry_date >= '$date') or (blc.subject_id != 23)";
		$db = Zend_Db_Table::getDefaultAdapter ();
		$select = $db->select ()->from ( array (
				'blq' => 'bal_questions' 
		), array (
				'blq.*' 
		) )->joinInner ( array (
				'blo' => 'bal_question_options' 
		), 'blq.bal_question_id = blo.question_id' )->joinLeft ( array (
				'bchs' => 'bal_child_question_sequence' 
		), 'blq.category_id = bchs.category_id', array (
				'bchs.category_id' 
		) )->joinInner ( array (
				'blc' => 'bal_question_categories' 
		), 'blq.category_id = blc.category_id', array (
				'blc.category_id',
				'blc.category_code' 
		) )->where ( "bchs.sequence_number = $currentSequence" )->where("blq.is_approved = 'Y'")->where($where)->where ( "bchs.child_id = $childId" )->where ( "blq.grade_id = '$gradId' OR blq.grade_id = 0" )->where ( "blo.answer = 'Y'" );
		$select->order ( new Zend_Db_Expr ( 'RAND()' ) );
		$select->limit ( 1 );
		$question = $db->fetchRow ( $select );
		return $question;
	}
	/**
	 * ******************function for fetch questions from parent set as chalanges***********************
	 */
	/**
	 * function used to fetch question
	 *
	 * @param
	 *        	int requestiId
	 * @return Array
	 */
	public function getQuestionsForChalanges($requestId) {
		$db = Zend_Db_Table::getDefaultAdapter ();
		$select = $db->select ()->from ( array (
				'blq' => 'bal_questions' 
		), array (
				'blq.*' 
		) )->joinLeft ( array (
				'bc' => 'bal_challenges' 
		), 'blq.bal_question_id = bc.question_id', array (
				'bc.request_id' 
		) )->where ( "bc.request_id  = $requestId" );
		$question = $db->fetchall ( $select );
		return $question;
	}
	/**
	 * function used to get subject id for the question
	 *
	 * @param
	 *        	int requestiId
	 * @return Array
	 */
	public function GetsubjectId($qId) {
		$db = Zend_Db_Table::getDefaultAdapter ();
		$select = $db->select ()->from ( array (
				'blq' => 'bal_questions' 
		), array (
				'blq.*' 
		) )->joinLeft ( array (
				'blc' => 'bal_question_categories' 
		), 'blq.category_id = blc.category_id', array (
				'blc.category_id',
				'blc.subject_id' 
		) )->where ( "blq.bal_question_id = $qId" );
		$question = $db->fetchrow ( $select );
		return $question;
	}
	
	/**
	 * function to get list of asked question
	 *
	 * @param
	 *        	int categoryId,gradeId,endDate,formDate
	 * @return Array List
	 * 
	 */
	public function getAskedQuestions($categoryId, $fromDate, $endDate, $gradeId) {
		if (! empty ( $categoryId )) {
			$where = "blq.category_id in ($categoryId)";
		} else {
			$where = "blqo.answer = 'Y'";
		}
		if (! empty ( $fromDate )) {
			$dateStart = date ( "Y-m-d 0:0:0", strtotime ( $fromDate ) );
			$where = $where . " and blqr.request_date >= '$dateStart'";
		}
		if (! empty ( $endDate )) {
			$dateEnd = date ( "Y-m-d 59:59:59", strtotime ( $endDate ) );
			$where = $where . " and blqr.request_date <= '$dateEnd'";
		}
		if (! empty ( $gradeId )) {
			$where = $where . " and (blq.grade_id = $gradeId or blq.grade_id = 0)";
		}
		$db = Zend_Db_Table::getDefaultAdapter ();
		/*
		 * $select = $db->select() ->from(array('blqr' => 'bal_child_question_requests'), array('blqr.request_id','blqr.request_date')) ->joinLeft(array('bcq' => 'bal_child_questions'), 'blqr.request_id = bcq.request_id', array('bcq.question_id','bcq.created_date')) ->joinLeft(array('blq' => 'bal_questions'), 'bcq.question_id = blq.bal_question_id', array('blq.question')) ->joinLeft(array('blqo' => 'bal_question_options'), 'blq.bal_question_id = blqo.question_id', array('blqo.option')) ->where($where) ->where("blqr.response_date is not null or blqr.request_type = 'C'") ->order(array("blqr.request_date desc","bcq.child_question_id desc")) ->group("bcq.question_id");
		 */
		
		$select = $db->select ()->from ( array (
				'blqr' => 'bal_child_question_requests' 
		), array (
				"max(`blqr`.`request_date`) as mid"
		) )->joinLeft ( array (
				'bcq' => 'bal_child_questions' 
		), 'blqr.request_id = bcq.request_id', array (
				"COUNT(bcq.question_id) as TotalCount",
				"SUM(if(blqr.points_type = 'A',1,0)) as TotalCorrect",
				"SUM(if(blqr.points_type = 'D',1,0)) as TotalWrong",
				"SUM(if(blqr.points_type IS NULL,1,0)) as UnAnswered",
				"SUM(if(blqr.points_type = 'A',`device_response_time`,0)) as CorrectAnswerTime",
				"SUM(if(blqr.points_type = 'D',`device_response_time`,0)) as WrongAnswerTime",
				"SUM(if(blqr.points_type is null,`device_response_time`,0)) as UnAnswerTime" 
		) )->joinLeft ( array (
				'blq' => 'bal_questions'
		), 'bcq.question_id = blq.bal_question_id', array (
				'blq.question','blq.question_equation_image_name','blq.question_display','blq.question_equation_images'
		) )->joinLeft ( array (
				'blqo' => 'bal_question_options' 
		), "blqo.question_id = blq.bal_question_id and blqo.answer = 'Y'", array (
				'blqo.option','blqo.option_equation_image_name','blqo.option_equation'
		) )->where ( $where )
                        //->where ( "blqr.device_response_time is not null or blqr.device_response_time != 0 or blqr.request_type = 'C'" )
                        ->group ( "bcq.question_id" )->order ( array (
				"mid DESC"
		) );
		$questionid = $db->fetchAll ( $select );
		return $questionid;
		/*SELECT `bcq`.`question_id`,max(`blqr`.`request_date`) as mid, 
`blq`.`question`, `blqo`.`option` 
FROM `bal_child_question_requests` AS `blqr` 
LEFT JOIN `bal_child_questions` AS `bcq` ON blqr.request_id = bcq.request_id 
LEFT JOIN `bal_questions` AS `blq` ON bcq.question_id = blq.bal_question_id 
LEFT JOIN `bal_question_options` AS `blqo` ON blqo.question_id = blq.bal_question_id AND blqo.answer = 'Y'
WHERE ((blqr.device_response_time IS NOT NULL AND blqr.device_response_time != 0) 
OR blqr.request_type = 'C')
GROUP by `bcq`.`question_id`
 ORDER BY mid DESC*/
		
	}
	/**
	 * function to get list of approved question
	 *
	 * @param
	 *        	int categoryId,gradeId
	 * @return Array List
	 */
	public function getQuestionsbyCatGrade($categoryId, $gradeId) {
		if(empty($gradeId) && empty($categoryId))
		{
			$question = array();
			return $question;
		}
		if ($gradeId != '') {
			$where = "(blq.grade_id = $gradeId or blq.grade_id = 0) and blq.is_approved = 'Y'";
		} else {
			$where = "blq.is_approved = 'Y'";
		}
		$todayDate 					= date("Y-m-d");//date return today server date and time.
		$where1 = "(blq.expiry_date > '$todayDate' or blq.expiry_date is null)";
		if ($categoryId == '') {
			$db = Zend_Db_Table::getDefaultAdapter ();
			$select = $db->select ()->from ( array (
					'blq' => 'bal_questions' 
			), array (
					'blq.bal_question_id',
					'blq.question',
					'blq.question_equation_image_name',
					'blq.modified_Date',
					'blq.created_date',
					'blq.approved_date',
					'blq.approved_by',
					'blq.created_by',
					'blq.modified_by',
					'blq.difficulty_level',
					'blq.question_display',
					'blq.question_equation_images'
			) )->joinLeft ( array (
					'blc' => 'bal_question_categories' 
			), 'blq.category_id = blc.category_id', array (
					'blc.category_id',
					'blc.category_code' 
			) )->joinLeft ( array (
					'blg' => 'bal_grades' 
			), 'blq.grade_id = blg.grades_id', array (
					'blg.grades_id',
					'blg.grade_name' 
			) )->where ( $where )->where($where1)->order ("blq.modified_by desc");
			$question = $db->fetchall ( $select );
		} else {
			$db = Zend_Db_Table::getDefaultAdapter ();
			$select = $db->select ()->from ( array (
					'blq' => 'bal_questions' 
			), array (
					'blq.bal_question_id',
					'blq.question',
					'blq.question_equation_image_name',
					'blq.modified_Date',
					'blq.created_date',
					'blq.approved_date',
					'blq.approved_by',
					'blq.created_by',
					'blq.modified_by',
					'blq.difficulty_level',
					'blq.question_display',
					'blq.question_equation_images'
			) )->joinLeft ( array (
					'blc' => 'bal_question_categories' 
			), 'blq.category_id = blc.category_id', array (
					'blc.category_id',
					'blc.category_code' 
			) )->joinLeft ( array (
					'blg' => 'bal_grades' 
			), 'blq.grade_id = blg.grades_id', array (
					'blg.grades_id',
					'blg.grade_name' 
			) )->where ( "blq.category_id in ($categoryId)" )->where ( $where )->where($where1)->order ("blq.date desc");
			$question = $db->fetchall ( $select );
		}
		return $question;
	}
	
	/**
	 * ***********************************block for new asked question fuction*********************
	 */
	/**
	 * function to get list of asked question of child
	 *
	 * @param
	 *        	int categoryId,childId,gradeId,endDate,formDate,mode
	 * @return Array List
	 */
	public function getAskedQuestionsOfChild($categoryId, $fromDate, $endDate, $gradeId, $modeOfQ, $childId,$sortCol=null,$sortOr=null,$sWhere = null) {
		if (! empty ( $categoryId )) {
			$where = "blq.category_id in ($categoryId)";
		} else {
			$where = "1";
		}
		if (! empty ( $fromDate )) {
			$dateStart = date ( "Y-m-d 0:0:0", strtotime ( $fromDate ) );
			$where = $where . " and blqr.request_date >= '$dateStart'";
		}
		if (! empty ( $endDate )) {
			$dateEnd = date ( "Y-m-d 59:59:59", strtotime ( $endDate ) );
			$where = $where . " and blqr.request_date <= '$dateEnd'";
		}
		if (! empty ( $gradeId )) {
			$where = $where . " and (blqr.grade_id in ($gradeId))";
		}
		if (! empty ( $modeOfQ )) {
			if ($modeOfQ != 'A') {
				$where = $where . " and blqr.request_type = '$modeOfQ'";
			}
		}
		if(!empty($sortCol) && $sortCol != null){
			if($sortCol == 'response_points'){
				$sortCol = "with_sign_response_points";
			}
			if($sortCol == 'request_type'){
				$sortCol = "FIELD( request_type,  'W',  'C',  'P',  'Q' )";
			}
			$order = $sortCol." ".$sortOr;
		}else{
			$order = "blqr.request_date desc";
		}
		if(!empty($sWhere) && $sWhere != null){
			$where .= "and $sWhere";
		}
		$where = $where . " and blqr.child_id = $childId";
		$db = Zend_Db_Table::getDefaultAdapter ();
		$select = $db->select ()->from ( array (
				'blqr' => 'bal_child_question_requests' 
		), array (
				'blqr.points_type',
				'blqr.response_points',
				"IF(points_type = 'A', blqr.response_points, blqr.response_points * -1) as with_sign_response_points",
				'blqr.request_type',
				new Zend_Db_Expr("CASE blqr.request_type WHEN 'W' THEN 'Challange1' WHEN 'P' THEN 'Challange2' WHEN 'C' THEN 'Challange3' WHEN 'Q' THEN 'Challange4' END AS question_mode"),
				'blqr.request_date',
				'blqr.device_response_time',
				'blqr.latitude',
				'blqr.longitude' 
		) )->joinLeft ( array (
				'bcq' => 'bal_child_questions' 
		), 'blqr.request_id = bcq.request_id', array (
				'bcq.question_id','answered_option_id'
		) )->joinLeft ( array (
				'blq' => 'bal_questions' 
		), 'bcq.question_id = blq.bal_question_id', array (
				'blq.question','blq.question_equation_image_name','blq.question_display','blq.question_equation_images'
		) )->joinLeft ( array (
				'blqo' => 'bal_question_options' 
		), 'blqo.question_option_id = bcq.answered_option_id', array (
				'blqo.option','blqo.option_equation_image_name','blqo.option_equation'
		) )->joinLeft ( array (
				'blc' => 'bal_question_categories' 
		), "blq.category_id = blc.category_id", array (
				'blc.category_code','blc.subtopic_name','blc.description as subtopic_description'
		) )->joinLeft ( array (
				'blqoo' => 'bal_question_options' 
		), "blqoo.answer = 'Y' and blqoo.question_id = blq.bal_question_id", array (
				'blqoo.question_option_id as right_option_id','blqoo.option as right_option','blqoo.option_equation_image_name as right_option_equation_image_name'
		,'blqoo.option_equation as right_option_equation'
		) )->where ( $where );
                
                $select->join(array('subject' => 'bal_subjects'), 'subject.subject_id = blc.subject_id', 'subject.subject_name');
                $select->join(array('domain' => 'bal_question_domains'), 'domain.domain_id = blc.domain_id', 'domain.name as domain_name');
                
                //->where ( "blqr.response_date is not null or (blqr.request_type = 'C' and blqr.response_points is not null)" );
                $select->order ($order);
		$questionid = $db->fetchAll ( $select );
		return $questionid;
	}
	/**
	 * *************************for asked question if all asked questions are displayed******************************
	 */
        
	public function GetQuestionRandomlySendChallenge($childId, $gradId, $subjectId, $level, $domainId, $exclude = '') {
            
            $db = Zend_Db_Table::getDefaultAdapter();
            $select = $db->select();
            $select->from(array('blq' => 'bal_questions'), array(
                'blq.bal_question_id', 'blq.question', 'blq.question_equation_image_name', 
                'blq.question_display', 'blq.question_equation_images'
            ));
            $select->joinLeft(array('blo' => 'bal_question_options'), 'blq.bal_question_id = blo.question_id', array(
                'question_option_id', 'blo.option', 'blo.option_equation_image_name', 'blo.option_equation'
            ));
            $select->joinLeft(array('blc' => 'bal_question_categories'), 'blq.category_id = blc.category_id', array('blc.category_id', 'blc.category_code'));
            
            $select->joinLeft(array('bchs' => 'bal_child_question_sequence'), 'blq.category_id = bchs.category_id', array ('bchs.category_id'));
            $select->where("bchs.child_id = ?", $childId);
            
            $select->where("(blc.subject_id = 23 and blq.expiry_date >= ?) or (blc.subject_id != 23)", date('Y-m-d'));
            $select->where("blq.grade_id = ? OR blq.grade_id = 0", $gradId);
            $select->where("blc.subject_id = ?", $subjectId);
            $select->where("blq.difficulty_level = ?", $level);
            $select->where("blq.is_approved = 'Y'");
            $select->where("blo.answer = 'Y'");
            
            if (!empty($domainId)) {
                $select->where("blc.domain_id = ?", $domainId);
            }
            
            if ($exclude != '') {
                $select->where("blq.bal_question_id not in ($exclude)");
            }
            
            $select->order(new Zend_Db_Expr('RAND()'));
            $select->limit(1);
            
            $question = $db->fetchRow($select);
            return $question;
        }

    /**
	 * function used to get question using search functionality on modifier and creator
	 * 
	 * @param 
	 *        	INT searchfield
	 * @return Array List
	 * @author Suman khatri 
	 */
	public function GetQuestionSearchCreatedandModifiedBy($searchby) {
		$todayDate 					= date("Y-m-d");//date return today server date and time.
		$where1 = "(blq.expiry_date > '$todayDate' or blq.expiry_date is null) and blq.is_approved = 'Y'";
		$db = Zend_Db_Table::getDefaultAdapter ();
		$select = $db->select ()->from ( array (
				'blq' => 'bal_questions' 
		), array (
				'blq.bal_question_id',
				'blq.question',
				'blq.question_equation_image_name',
				'blq.modified_Date',
				'blq.created_date',
				'blq.approved_date',
				'blq.approved_by',
				'blq.created_by',
				'blq.modified_by',
				'blq.question_display',
				'blq.question_equation_images'
		) )->joinLeft ( array (
				'blc' => 'bal_question_categories' 
		), 'blq.category_id = blc.category_id', array (
				'blc.category_id',
				'blc.category_code' 
		) )->joinLeft ( array (
				'blg' => 'bal_grades' 
		), 'blq.grade_id = blg.grades_id', array (
				'blg.grades_id',
				'blg.grade_name' 
		) )->where ( "blq.created_byid = '$searchby' or blq.modified_byid = '$searchby'")->where($where1)->order ("blq.date desc");
		$question = $db->fetchall ( $select );
		return $question;
	}
	

	/**
	 * *********************function for admin
	 * can seen stats
	 * 
	 * @param
	 *        	gradesid
	 * @return array; ***********************
	 */
	public function getquestionStats($gradeId, $subjectId) {
		if(!empty($subjectId)){
			$where = "bqc.subject_id = $subjectId";
		}else{
			$where = 1;
		}
		$db = Zend_Db_Table::getDefaultAdapter ();
		$select = $db->select ()->from ( array (
				'blq' => $this->_name 
		), array (
				'total' => 'count(*)',
				"SUM(if(blq.is_approved = 'Y',1,0)) AS totalApproved",
				'subject_id' => 'bqc.subject_id' 
		) )->joinLeft ( array (
				'bqc' => 'bal_question_categories' 
		), 'blq.category_id = bqc.category_id', array () )->joinLeft ( array (
				'bqcg' => 'bal_question_category_grades' 
		), "bqcg.category_id = bqc.category_id AND bqcg.grade_id = $gradeId", array () )
		->where ( "blq.grade_id = $gradeId OR blq.grade_id = 0" )->where ( "(blq.grade_id = $gradeId  OR blq.grade_id = 0) And bqcg.grade_id IS NOT NULL" )
		->where($where)
		->group ( "bqc.subject_id" );
		$question = $db->fetchAll ( $select );
		return $question;
	}
	
 public function getCreatedDate($userId){
 	$db = Zend_Db_Table::getDefaultAdapter ();
 	$selectDate = $db->select ()->from ( array (
 			'blq' => $this->_name
 	), array (
 			'minDate' => 'MIN(blq.created_date)',
 			'MaxDate' => 'MAX(blq.created_date)'
 	) )->where("created_byid = $userId");
 	$dateArray =$db->fetchAll($selectDate);
 	return  $dateArray;
 	
 }	
	
	
	public function getAllUserWiseData($userId,$fromDate,$startDate){
		
		$db = Zend_Db_Table::getDefaultAdapter ();
		$select = $db->select ()->from ( array (
				'blq' => $this->_name
		), array (
				'total' => 'count(*)',
				"SUM(if(blq.is_approved = 'Y',1,0)) AS totalApproved",
				'subject_id' => 'bqc.subject_id'
		) )->joinLeft ( array (
				'bqc' => 'bal_question_categories'
		), 'blq.category_id = bqc.category_id', array () )
		->where("blq.created_date <='$startDate'")
		->where("blq.created_date >'$fromDate'")
		->where("blq.created_byid = '$userId'")
		//->where("blq.is_approved = 'Y'")
		->group ( "bqc.subject_id" );
		$question = $db->fetchAll ( $select );
		return $question;
		
	}
	
	public function getCountData($userId, $dateRange, $todayDate) {
		$startDate = $dateRange ['start_date'];
		$endDate = $dateRange ['end_date'];
		$lastweekS = $dateRange ['last_satart'];
		$lastweekE = $dateRange ['last_end'];
		$lastmStart = $dateRange ['lastm_start'];
		$lastmEnd = $dateRange ['lastm_end'];
		$where = "blq.created_date >= '$startDate' AND blq.created_date <= '$endDate' AND blq.created_byid = $userId";
		$where1 = "blq.created_date >= '$lastweekS' AND blq.created_date <= '$lastweekE' AND blq.created_byid = $userId";
		$where2 = "blq.created_date >= '$lastmStart' AND blq.created_date <= '$lastmEnd' AND blq.created_byid = $userId";
		$where3 = "blq.created_byid = $userId AND edited_question = '1' AND blq.created_date < '$todayDate'";
		$where4 = "blq.created_date < '$todayDate' AND blq.created_byid = $userId";
		$db = Zend_Db_Table::getDefaultAdapter ();
		$select = $db->select ()->from ( array (
				'blq' => $this->_name 
		), array (
				'total' => 'count(*)',
				"SUM(if(blq.is_approved = 'Y',1,0)) AS totalApproved" 
		) )->where ( $where );
		$db = Zend_Db_Table::getDefaultAdapter ();
		$select1 = $db->select ()->from ( array (
				'blq' => $this->_name 
		), array (
				'total' => 'count(*)',
				"SUM(if(blq.is_approved = 'Y',1,0)) AS totalApproved"
		)
		 )->where ( $where1 );
		$db = Zend_Db_Table::getDefaultAdapter ();
		$select2 = $db->select ()->from ( array (
				'blq' => $this->_name 
		), array (
				'total' => 'count(*)',
				"SUM(if(blq.is_approved = 'Y',1,0)) AS totalApproved"
		) )->where ( $where2 );
		$db = Zend_Db_Table::getDefaultAdapter ();
		$select3 = $db->select ()->from ( array (
				'blq' => $this->_name 
		), array (
				'total' => 'count(*)',
				"SUM(if(blq.is_approved = 'Y',1,0)) AS totalApproved"
		) )->where ( $where3 );
		$select4 = $db->select ()->from ( array (
				'blq' => $this->_name 
		), array (
				'total' => 'count(*)',
				"SUM(if(blq.is_approved = 'Y',1,0)) AS totalApproved"
		) )->where ( $where4 );
		$dataResult ['this_week'] = $db->fetchAll ( $select );
		$dataResult ['last_week'] = $db->fetchAll ( $select1 );
		$dataResult ['last_month'] = $db->fetchAll ( $select2 );
		$dataResult ['total_question'] = $db->fetchAll ( $select4 );
		$dataResult ['questions_edited'] = $db->fetchAll ( $select3 );
		return $dataResult;
	}
	/*********************functin for send all question id of url_question_id is null**/
	public function fetchAllQuestionId(){
		$db = Zend_Db_Table::getDefaultAdapter ();
		$select = $db->select ()->from ( array (
				'blq' => $this->_name
		), array (
				'blq.bal_question_id',
		) )->where("blq.url_of_question =''");
		$result = $db->fetchAll($select);
		return $result;
	}
	 
	
	public function getQuestionsForNotification($todayDate,$endDateWeek)
	{
		$where = "(blc.subject_id = 23 and blq.expiry_date >= '$todayDate' and blq.expiry_date <= '$endDateWeek')";
		$db = Zend_Db_Table::getDefaultAdapter ();
		$select = $db->select ()->from ( array (
				'blq' => 'bal_questions'
		), array (
				'blq.question','blq.expiry_date','blq.bal_question_id'
		) )->joinLeft ( array (
				'blc' => 'bal_question_categories'
		), 'blq.category_id = blc.category_id', array (
				'blc.category_id',
				'blc.category_code','blc.category_code'
		) )->joinLeft ( array (
				'bls' => 'bal_subjects'
		), 'bls.subject_id = blc.subject_id', array (
				'bls.subject_name',
		) )
		->where($where)->where("blq.is_approved = 'Y'");
		$select->order ("blq.expiry_date desc");
		$question = $db->fetchAll($select);
		return $question;
	}
	
	/**
	 * function used to get expiring question 
	 * 
	 * @param 
	 *        	 $fromDate,$endDate
	 * @return Array List
	 * @author Suman khatri 
	 */
	public function getQuestionsForExpiring($fromDate,$endDate) {
		$todayDate 					= date("Y-m-d");//date return today server date and time.
		$where = "blc.subject_id = 23 and blq.expiry_date >= '$todayDate'";
		if(!empty($fromDate)) {
			$where = "blc.subject_id = 23 and blq.expiry_date >= '$fromDate' ";
		}
		if(!empty($endDate)) {
			$where .= "and blq.expiry_date <= '$endDate'";
		}
		$db = Zend_Db_Table::getDefaultAdapter ();
		$select = $db->select ()->from ( array (
				'blq' => 'bal_questions' 
		), array (
				'blq.bal_question_id',
				'blq.question',
				'blq.question_equation_image_name',
				'blq.modified_Date',
				'blq.created_date',
				'blq.created_by',
				'blq.modified_by',
				'blq.difficulty_level' 
		) )->joinLeft ( array (
				'blc' => 'bal_question_categories' 
		), 'blq.category_id = blc.category_id', array (
				'blc.category_id',
				'blc.category_code' 
		) )->joinLeft ( array (
				'blg' => 'bal_grades' 
		), 'blq.grade_id = blg.grades_id', array (
				'blg.grades_id',
				'blg.grade_name' 
		) )->where ( $where )->where("blq.is_approved = 'Y'")->order ("blq.expiry_date asc");
		$question = $db->fetchall ( $select );
		return $question;
	}
	
/**
	 * function used to get expiring question using search functionality
	 *
	 * @param
	 *        	varchar searchfield
	 * @return Array List
	 */
	public function getQuestionsbysearchForExpiring($search_field) {
		$todayDate 					= date("Y-m-d");//date return today server date and time.
		$where = "blc.subject_id = 23 and blq.expiry_date >= '$todayDate'";
		$where1 =  $this->_db->quoteInto("blq.question like?","%$search_field%");
		$db = Zend_Db_Table::getDefaultAdapter ();
		$select = $db->select ()->from ( array (
				'blq' => 'bal_questions' 
		), array (
				'blq.bal_question_id',
				'blq.question',
				'blq.question_equation_image_name',
				'blq.modified_Date',
				'blq.created_date',
				'blq.created_by',
				'blq.modified_by' 
		) )->joinLeft ( array (
				'blc' => 'bal_question_categories' 
		), 'blq.category_id = blc.category_id', array (
				'blc.category_id',
				'blc.category_code' 
		) )->joinLeft ( array (
				'blg' => 'bal_grades' 
		), 'blq.grade_id = blg.grades_id', array (
				'blg.grades_id',
				'blg.grade_name' 
		) )->where($where)->where ($where1)->where("blq.is_approved = 'Y'")->order ("blq.expiry_date asc");
		$question = $db->fetchall ( $select );
		return $question;
	}
	
/**
	 * function used to get expired question 
	 * 
	 * @param 
	 *        	 $fromDate,$endDate
	 * @return Array List
	 * @author Suman khatri 
	 */
	public function getQuestionsForExpired() {
		$todayDate 					= date("Y-m-d");//date return today server date and time.
		$where = "blc.subject_id = 23 and blq.expiry_date < '$todayDate' and (blq.copy_created = 'F' OR blq.copy_created is NULL)";
		
		$db = Zend_Db_Table::getDefaultAdapter ();
		$select = $db->select ()->from ( array (
				'blq' => 'bal_questions' 
		), array (
				'blq.bal_question_id',
				'blq.question',
				'blq.question_equation_image_name',
				'blq.modified_Date',
				'blq.created_date',
				'blq.created_by',
				'blq.modified_by',
				'blq.difficulty_level' 
		) )->joinLeft ( array (
				'blc' => 'bal_question_categories' 
		), 'blq.category_id = blc.category_id', array (
				'blc.category_id',
				'blc.category_code' 
		) )->joinLeft ( array (
				'blg' => 'bal_grades' 
		), 'blq.grade_id = blg.grades_id', array (
				'blg.grades_id',
				'blg.grade_name' 
		) )->where ( $where )->order ("blq.date desc");
                
		$question = $db->fetchall ( $select );
		return $question;
	}
	
/**
	 * function used to get question using search functionality for expired question
	 *
	 * @param
	 *        	varchar searchfield
	 * @return Array List
	 */
	public function getQuestionsbysearchForExpired($search_field) {
		$todayDate 					= date("Y-m-d");//date return today server date and time.
		$where = "blc.subject_id = 23 and blq.expiry_date < '$todayDate' and blq.copy_created = 'F'";
		$where1 =  $this->_db->quoteInto("blq.question like?","%$search_field%");
		$db = Zend_Db_Table::getDefaultAdapter ();
		$select = $db->select ()->from ( array (
				'blq' => 'bal_questions' 
		), array (
				'blq.bal_question_id',
				'blq.question',
				'blq.question_equation_image_name',
				'blq.modified_Date',
				'blq.created_date',
				'blq.created_by',
				'blq.modified_by' 
		) )->joinLeft ( array (
				'blc' => 'bal_question_categories' 
		), 'blq.category_id = blc.category_id', array (
				'blc.category_id',
				'blc.category_code' 
		) )->joinLeft ( array (
				'blg' => 'bal_grades' 
		), 'blq.grade_id = blg.grades_id', array (
				'blg.grades_id',
				'blg.grade_name' 
		) )->where($where)->where ($where1)->order ("blq.date desc");
		$question = $db->fetchall ( $select );
		return $question;
	}
	
/**
	 * function to get list of draft question
	 *
	 * @param
	 *        	int categoryId,gradeId
	 * @return Array List
	 */
	public function getDraftQuestionsbyCatGrade($categoryId, $gradeId) {
		if(empty($gradeId) && empty($categoryId))
		{
			$question = array();
			return $question;
		}
		if ($gradeId != '') {
			$where = "(blq.grade_id = $gradeId or blq.grade_id = 0) and blq.is_approved = 'N'";
		} else {
			$where = "blq.is_approved = 'N'";
		}
		$todayDate 					= date("Y-m-d");//date return today server date and time.
		//$where1 = "(blq.expiry_date > '$todayDate' or blq.expiry_date is null)";
                $where1 = "1";
		if ($categoryId == '') {
			$db = Zend_Db_Table::getDefaultAdapter ();
			$select = $db->select ()->from ( array (
					'blq' => 'bal_questions' 
			), array (
					'blq.bal_question_id',
					'blq.question',
					'blq.question_equation_image_name',
					'blq.modified_Date',
					'blq.created_date',
					'blq.created_by',
					'blq.modified_by',
					'blq.difficulty_level',
					'blq.question_display',
					'blq.question_equation_images'
			) )->joinLeft ( array (
					'blc' => 'bal_question_categories' 
			), 'blq.category_id = blc.category_id', array (
					'blc.category_id',
					'blc.category_code' 
			) )->joinLeft ( array (
					'blg' => 'bal_grades' 
			), 'blq.grade_id = blg.grades_id', array (
					'blg.grades_id',
					'blg.grade_name' 
			) )->where ( $where )->where($where1)->order ("blq.date desc");
			$question = $db->fetchall ( $select );
		} else {
			$db = Zend_Db_Table::getDefaultAdapter ();
			$select = $db->select ()->from ( array (
					'blq' => 'bal_questions' 
			), array (
					'blq.bal_question_id',
					'blq.question',
					'blq.question_equation_image_name',
					'blq.modified_Date',
					'blq.created_date',
					'blq.created_by',
					'blq.modified_by',
					'blq.difficulty_level',
					'blq.question_display',
					'blq.question_equation_images'
			) )->joinLeft ( array (
					'blc' => 'bal_question_categories' 
			), 'blq.category_id = blc.category_id', array (
					'blc.category_id',
					'blc.category_code' 
			) )->joinLeft ( array (
					'blg' => 'bal_grades' 
			), 'blq.grade_id = blg.grades_id', array (
					'blg.grades_id',
					'blg.grade_name' 
			) )->where ( "blq.category_id in ($categoryId)" )->where ( $where )->where($where1)->order ("blq.date desc");
			$question = $db->fetchall ( $select );
		}
		return $question;
	}
	
/**
	 * function used to get draft question using search functionality
	 *
	 * @param
	 *        	varchar searchfield
	 * @return Array List
	 */
	public function getDraftQuestionsbysearch($search_field) {
		$todayDate 					= date("Y-m-d");//date return today server date and time.
		//$where1 = "(blq.expiry_date > '$todayDate' or blq.expiry_date is null) and blq.is_approved = 'N'";
                $where1 = "blq.is_approved = 'N'";
		$where2 =  $this->_db->quoteInto("blq.question like?","%$search_field%");
		$db = Zend_Db_Table::getDefaultAdapter ();
		$select = $db->select ()->from ( array (
				'blq' => 'bal_questions' 
		), array (
				'blq.bal_question_id',
				'blq.question',
				'blq.question_equation_image_name',
				'blq.modified_Date',
				'blq.created_date',
				'blq.created_by',
				'blq.modified_by',
				'blq.question_display',
				'blq.question_equation_images' 
		) )->joinLeft ( array (
				'blc' => 'bal_question_categories' 
		), 'blq.category_id = blc.category_id', array (
				'blc.category_id',
				'blc.category_code' 
		) )->joinLeft ( array (
				'blg' => 'bal_grades' 
		), 'blq.grade_id = blg.grades_id', array (
				'blg.grades_id',
				'blg.grade_name' 
		) )->where ($where2)->where($where1)->order ("blq.date desc");
		$question = $db->fetchall ( $select );
		return $question;
	}
	
/**
	 * function used to get draft question using search functionality on modifier and creator
	 * 
	 * @param 
	 *        	INT searchfield
	 * @return Array List
	 * @author Suman khatri 
	 */
	public function GetDraftQuestionSearchCreatedandModifiedBy($searchby) {
		$todayDate 					= date("Y-m-d");//date return today server date and time.
		//$where1 = "(blq.expiry_date > '$todayDate' or blq.expiry_date is null) and blq.is_approved = 'N'";
                $where1 = "blq.is_approved = 'N'";
		$db = Zend_Db_Table::getDefaultAdapter ();
		$select = $db->select ()->from ( array (
				'blq' => 'bal_questions' 
		), array (
				'blq.bal_question_id',
				'blq.question',
				'blq.question_equation_image_name',
				'blq.modified_Date',
				'blq.created_date',
				'blq.created_by',
				'blq.modified_by',
				'blq.question_display',
				'blq.question_equation_images'
		) )->joinLeft ( array (
				'blc' => 'bal_question_categories' 
		), 'blq.category_id = blc.category_id', array (
				'blc.category_id',
				'blc.category_code' 
		) )->joinLeft ( array (
				'blg' => 'bal_grades' 
		), 'blq.grade_id = blg.grades_id', array (
				'blg.grades_id',
				'blg.grade_name' 
		) )->where ( "blq.created_byid = '$searchby'")->where($where1)->order ("blq.date desc");
		$question = $db->fetchall ( $select );
		return $question;
	}
	
	
	/**
	 * function to get report month wise
	 * @param int childId , $gradeId , array $subId
	 * @author Suman Khatri on 6th November 2013
	 * @return Array List
	 */
	public function GetMonthlyQuestionForReport($subId, $childId, $gradeId ,$firstDate , $lastDate ,$sOrder = null,$sortOr= null,$sWhere = null) {
		$resultMonthArray = array();
		$where = "blqr.child_id = $childId";
		if(!empty($sOrder) && $sOrder != null){
			$order = $sOrder." ".$sortOr;
		}else{
			$order = "";
		}
		if(!empty($sWhere) && $sWhere != null){
			$where .= " and $sWhere";
		}
		$k = 1;
		$resultSubjectArray = array();
		$subjectwiseData1 = array();
		foreach ($subId as $sujectId){
			if(!empty($sujectId) && $sujectId != null){
				$where1 = "blqr.grade_id = $gradeId and blqr.request_date >= '$firstDate' and blqr.request_date <= '$lastDate'";
				$db = Zend_Db_Table::getDefaultAdapter ();
				$select = $db->select ()->from ( array (
						'blqr' => 'bal_child_question_requests' 
				), array (
						"request_id"
				) )->joinLeft ( array (
						'bcq' => 'bal_child_questions' 
				), 'blqr.request_id = bcq.request_id', array (
						"COUNT(bcq.question_id) as TotalCount",
						"SUM(if(blqr.points_type = 'A',1,0)) as TotalCorrect",
						"(SUM(if(blqr.points_type = 'A',1,0))/COUNT(bcq.question_id))*100 as perCentage"
				) )->joinLeft ( array (
						'blq' => 'bal_questions' 
				), 'bcq.question_id = blq.bal_question_id', array (
						'blq.question'
				) )->joinLeft ( array (
						'blc' => 'bal_question_categories' 
				), "blq.category_id = blc.category_id", array (
						'blc.category_code','blc.psv_code'
				) )->joinLeft ( array (
						'bls' => 'bal_subjects' 
				), "bls.subject_id = $sujectId", array (
						'bls.subject_name'
				) )->joinLeft ( array (
						'bld' => 'bal_question_domains' 
				), "bld.domain_id = blc.domain_id", array (
						'bld.code','bld.name as dName'
				) )
				->joinLeft ( array (
						'blsd' => 'bal_standards' 
				), "blsd.standard_id = blc.standard_id", array (
						'blsd.name as stdName'
				) )->joinLeft ( array (
						'blg' => 'bal_grades' 
				), "blg.grades_id = $gradeId or blg.grades_id = 0", array (
						'blg.grade_name as gradeName'
				) )->where ( $where )->where("blc.subject_id = $sujectId")->where($where1)
				//->where("blqr.response_date is not null or (blqr.request_type = 'C' and blqr.response_points is not null)")
                                ->order($order);
				$subjectwiseData = $db->fetchAll ( $select );//echo "<pre>";print_r($subjectwiseData);die;
				if($subjectwiseData[0]['TotalCount'] > 0) {
				$subjectwiseData1['subjectId'] = $sujectId;
				$subjectwiseData1['startDate'] = $firstDate;
				$subjectwiseData1['lastDate'] = $lastDate;
				if($subjectwiseData[0]['gradeName'] == 0){
					$gradesName = '1-12';
				}else{
					$gradesName = $subjectwiseData[0]['gradeName'];
				}
				
                                //$subjectwiseData1['categoryCode'] = strtoupper($subjectwiseData[0]['stdName'].".".$subjectwiseData[0]['subject_name'].".".$gradesName.".".$subjectwiseData[0]['code']);
				//$subjectwiseData1['categoryCode'] .= "<br>(".$subjectwiseData[0]['dName'].")";
                                
                                $subjectwiseData1['categoryCode'] = $subjectwiseData[0]['subject_name'];
				
                                if(strtolower($subjectwiseData1['categoryCode']) == 'math.content') {
                                    $subjectwiseData1['categoryCode'] = 'math';
                                }
                                
                                $subjectwiseData1['categoryCode'] = ucwords($subjectwiseData1['categoryCode']);
                                
                                $subjectwiseData1['totalCount'] = $subjectwiseData[0]['TotalCount'];
				$subjectwiseData1['totalCorrect'] = $subjectwiseData[0]['TotalCorrect'];
				$subjectwiseData1['perCentage'] = $subjectwiseData[0]['perCentage'];
				$subjectwiseData1['cateName'] = strtoupper($subjectwiseData[0]['stdName'].".".$subjectwiseData[0]['subject_name'].".".$gradesName.".".$subjectwiseData[0]['code']);
				$resultSubjectArray[$k] = $subjectwiseData1;
				unset($subjectwiseData1);
				$k++;
				}
			}else{
				continue;
			}
		}
		return $resultSubjectArray; 
	}
	
/**
	 * function to get report category wise
	 * @param int childId , $gradeId , array $catId
	 * @author Suman Khatri on 7th November 2013
	 * @return Array List
	 */
	public function GetMonthlyQuestionForReportCategoryWise($catId, $childId, $gradeId, $firstDate, $lastDate,
	$sOrder=null ,$sortOr=null,$sWhere =null, $subjectId = null) {
		$resultMonthArray = array();
		$where = "blqr.child_id = $childId";
		$where = "blqr.child_id = $childId";
		if(!empty($sOrder) && $sOrder != null){
			$order = $sOrder." ".$sortOr;
		}else{
			$order = "";
		}
		if(!empty($sWhere) && $sWhere != null){
			$where .= " and $sWhere";
		}
		$k = 1;
		$firstDate = date("Y-m-d 00:00:00",strtotime($firstDate));
		$lastDate = date("Y-m-d 23:59:59" ,strtotime($lastDate));
		$resultSubjectArray = array();
		$subjectwiseData1 = array();
		foreach ($catId as $categoryId){
			$where1 = "blqr.grade_id = $gradeId and blqr.request_date >= '$firstDate' and blqr.request_date <= '$lastDate'";
			$db = Zend_Db_Table::getDefaultAdapter ();
			$select = $db->select ()->from ( array (
					'blqr' => 'bal_child_question_requests' 
			), array (
					"request_id"
			) )->joinLeft ( array (
					'bcq' => 'bal_child_questions' 
			), 'blqr.request_id = bcq.request_id', array (
					"COUNT(bcq.question_id) as TotalCount",
					"SUM(if(blqr.points_type = 'A',1,0)) as TotalCorrect",
					"(SUM(if(blqr.points_type = 'A',1,0))/COUNT(bcq.question_id))*100 as perCentage"
			) )->joinLeft ( array (
					'blq' => 'bal_questions' 
			), "bcq.question_id = blq.bal_question_id", array (
					'blq.question'
			) )->joinLeft ( array (
					'blc' => 'bal_question_categories' 
			), "blq.category_id = blc.category_id", array (
					'blc.category_code','blc.psv_code','blc.subtopic_name'
			) )->where ( $where )->where($where1)->where("blc.category_id = $categoryId");
                        
                        $select->joinLeft(array('domain' => 'bal_question_domains'), 'domain.domain_id = blc.domain_id', array('domain.domain_id as domain_id', 'domain.name as domain_name'));
                        $select->joinLeft(array('blsd' => 'bal_standards'), "blsd.standard_id = blc.standard_id", array('blsd.name as stdName'));
                        $select->joinLeft(array('bls' => 'bal_subjects'), "bls.subject_id = $subjectId", array('bls.subject_name'));
                        $select->joinLeft(array('blg' => 'bal_grades'), "blg.grades_id = $gradeId or blg.grades_id = 0", array('blg.grade_name as gradeName'));
                        $select->joinLeft(array('bld' => 'bal_question_domains'), "bld.domain_id = blc.domain_id", array('bld.code'));
                        
                        //->where ("blqr.response_date is not null or (blqr.request_type = 'C' and blqr.response_points is not null)");
			$subjectwiseData = $db->fetchAll ( $select );//echo "<pre>";print_r($subjectwiseData);die;
			if($subjectwiseData[0]['TotalCount'] > 0) {
			$subjectwiseData1['categoryId'] = $categoryId;
			$subjectwiseData1['startDate'] = $firstDate;
			$subjectwiseData1['lastDate'] = $lastDate;
			$subjectwiseData1['categoryCode'] = $subjectwiseData[0]['category_code'];
			$subjectwiseData1['description'] = $subjectwiseData[0]['subtopic_name'];
			$subjectwiseData1['totalCount'] = $subjectwiseData[0]['TotalCount'];
			$subjectwiseData1['totalCorrect'] = $subjectwiseData[0]['TotalCorrect'];
			$subjectwiseData1['perCentage'] = $subjectwiseData[0]['perCentage'];

                        if($subjectwiseData[0]['gradeName'] == 0){
                            $gradesName = '1-12';
                        }else{
                            $gradesName = $subjectwiseData[0]['gradeName'];
                        }
                        
                        $subjectwiseData1['domain_id'] = $subjectwiseData[0]['domain_id'];
			$subjectwiseData1['domain_name'] = $subjectwiseData[0]['domain_name'];
                        $subjectwiseData1['domain_code'] = strtoupper($subjectwiseData[0]['stdName'].".".$subjectwiseData[0]['subject_name'].".".$gradesName.".".$subjectwiseData[0]['code']);
                        
                        //$subjectwiseData1['domain_name'] = $subjectwiseData1['domain_code']. ' ('.$subjectwiseData1['domain_name'].') ';
                        
			$resultSubjectArray[$subjectwiseData1['domain_id']][] = $subjectwiseData1;
			unset($subjectwiseData1);
			$k++;
			}
			
		}
                //echo '<pre>';print_r($resultSubjectArray);exit;
		return $resultSubjectArray;
	}
	
/**
	 * function to get list of asked question of child
	 *
	 * @param
	 *        	int categoryId,childId,gradeId,endDate,formDate
	 * @return Array List
	 */
	public function getAskedQuestionsOfChildforReport($categoryId, $firstDate, $lastDate, $gradeId, $childId,
	$sortCol=null,$sortOr=null,$sWhere = null) {
		$where = "blq.category_id = $categoryId";
		if (! empty ( $firstDate )) {
			$dateStart = date ( "Y-m-d 0:0:0", strtotime ( $firstDate ) );
			$where = $where . " and blqr.request_date >= '$dateStart'";
		}
		if (! empty ( $lastDate )) {
			$dateEnd = date ( "Y-m-d 59:59:59", strtotime ( $lastDate ) );
			$where = $where . " and blqr.request_date <= '$dateEnd'";
		}
		$where = $where . " and (blq.grade_id = $gradeId or blq.grade_id = 0)";
		$where = $where . " and blqr.child_id = $childId";
		if(!empty($sortCol) && $sortCol != null){
			if($sortCol == 'blqr.response_points'){
				$sortCol = "with_sign_response_points";
			}
			if($sortCol == 'blqo.right_option'){
				$sortCol = "correct_answer";
			}
			$order = $sortCol." ".$sortOr;
		}else{
			$order = "blqr.request_date desc";
		}
		if(!empty($sWhere) && $sWhere != null){
			$where .= " and $sWhere";
		}
		$db = Zend_Db_Table::getDefaultAdapter ();
		$select = $db->select ()->from ( array (
				'blqr' => 'bal_child_question_requests' 
		), array (
				/*'blqr.request_id',*/
				'blqr.points_type',
				'blqr.response_points',
				'blqr.request_type',
				'blqr.request_date',
				'blqr.device_response_time',
				"IF(points_type = 'A', blqr.response_points, blqr.response_points * -1) as with_sign_response_points",
				'blqr.latitude',
				'blqr.longitude' 
		) )->joinLeft ( array (
				'bcq' => 'bal_child_questions' 
		), 'blqr.request_id = bcq.request_id', array (
				'bcq.question_id','answered_option_id' 
		) )->joinLeft ( array (
				'blq' => 'bal_questions' 
		), 'bcq.question_id = blq.bal_question_id', array (
				'blq.question','blq.question_equation_image_name','blq.question_display','blq.question_equation_images'
		) )->joinLeft ( array (
				'blqo' => 'bal_question_options' 
		), 'blqo.question_option_id = bcq.answered_option_id', array (
				'blqo.option','blqo.option_equation_image_name','blqo.option_equation'
		) )->joinLeft ( array (
				'blc' => 'bal_question_categories' 
		), "blq.category_id = blc.category_id", array (
				'blc.category_code', 'blc.description as subtopic_description'
		) )->joinLeft ( array (
				'blqoo' => 'bal_question_options' 
		), "blqoo.answer = 'Y' and blqoo.question_id = blq.bal_question_id", array (
				'blqoo.question_option_id as right_option_id','blqoo.option as right_option','blqoo.option_equation_image_name as right_option_equation_image_name'
		,'blqoo.option_equation as right_option_equation'
		) )->where ( $where )//->where ( "blqr.response_date is not null or (blqr.request_type = 'C' and blqr.response_points is not null)" )
                     ->order ($order);
                
                
                $select->join(array('subject' => 'bal_subjects'), 'subject.subject_id = blc.subject_id', 'subject.subject_name');
                $select->join(array('domain' => 'bal_question_domains'), 'domain.domain_id = blc.domain_id', 'domain.name as domain_name');
                                
		$questionid = $db->fetchAll ( $select );
		return $questionid;
	}
	
	/**
	 * function to get report quarter wise
	 * @param int childId , $gradeId , array $subId
	 * @author Suman Khatri on 6th November 2013
	 * @return Array List
	 */
	public function GetQuarterlyQuestionForReport($subId, $childId, $gradeId,$qdates,$sOrder = null,$sortOr= null,$sWhere = null) {
		$resultMonthArray = array();
		$dates = explode("&",$qdates);
		$firstDate= $dates[0];
		$lastDate = $dates[1];//echo "<pre>";print_r($dates);
		$k = 1;
		$resultSubjectArray = array();
		$subjectwiseData1 = array();
		$where1 = "blqr.grade_id = $gradeId and blqr.request_date >= '$firstDate' and blqr.request_date <= '$lastDate'";
		$where = "blqr.child_id = $childId";
		if(!empty($sOrder) && $sOrder != null){
			$order = $sOrder." ".$sortOr;
		}else{
			$order = "";
		}
		if(!empty($sWhere) && $sWhere != null){
			$where .= " and $sWhere";
		}
		foreach ($subId as $sujectId){
			if(!empty($sujectId) && $sujectId != null){
				$db = Zend_Db_Table::getDefaultAdapter ();
				$select = $db->select ()->from ( array (
						'blqr' => 'bal_child_question_requests' 
				), array (
						"request_id"
				) )->joinLeft ( array (
						'bcq' => 'bal_child_questions' 
				), 'blqr.request_id = bcq.request_id', array (
						"COUNT(bcq.question_id) as TotalCount",
						"SUM(if(blqr.points_type = 'A',1,0)) as TotalCorrect",
						"(SUM(if(blqr.points_type = 'A',1,0))/COUNT(bcq.question_id))*100 as perCentage"
				) )->joinLeft ( array (
						'blq' => 'bal_questions' 
				), 'bcq.question_id = blq.bal_question_id', array (
						'blq.question'
				) )->joinLeft ( array (
						'blc' => 'bal_question_categories' 
				), "blq.category_id = blc.category_id", array (
						'blc.psv_code','blc.category_code'
				) )->joinLeft ( array (
						'bls' => 'bal_subjects' 
				), "bls.subject_id = $sujectId", array (
						'bls.subject_name'
				) )->joinLeft ( array (
						'bld' => 'bal_question_domains' 
				), "bld.domain_id = blc.domain_id", array (
						'bld.code','bld.name as dName'
				) )->joinLeft ( array (
						'blsd' => 'bal_standards' 
				), "blsd.standard_id = blc.standard_id", array (
						'blsd.name as stdName'
				) )->joinLeft ( array (
						'blg' => 'bal_grades' 
				), "blg.grades_id = $gradeId or blg.grades_id = 0", array (
						'blg.grade_name as gradeName'
				) )->where($where)->where("blc.subject_id = $sujectId")->where($where1)
				//->where ("blqr.response_date is not null or (blqr.request_type = 'C' and blqr.response_points is not null)")
                                ->order($order);
				$subjectwiseData = $db->fetchAll ( $select );
				if($subjectwiseData[0]['TotalCount'] > 0) {
				$subjectwiseData1['subjectId'] = $sujectId;
				$subjectwiseData1['startDate'] = $firstDate;
				$subjectwiseData1['lastDate'] = $lastDate;
				if($subjectwiseData[0]['gradeName'] == 0){
					$gradesName = '1-12';
				}else{
					$gradesName = $subjectwiseData[0]['gradeName'];
				}
                                
				//$subjectwiseData1['categoryCode'] = strtoupper($subjectwiseData[0]['stdName'].".".$subjectwiseData[0]['subject_name'].".".$gradesName.".".$subjectwiseData[0]['code']);
				//$subjectwiseData1['categoryCode'] .= "<br>(".$subjectwiseData[0]['dName'].")";
                                                                
                                $subjectwiseData1['categoryCode'] = $subjectwiseData[0]['subject_name'];
				
                                if(strtolower($subjectwiseData1['categoryCode']) == 'math.content') {
                                    $subjectwiseData1['categoryCode'] = 'math';
                                }
                                
                                $subjectwiseData1['categoryCode'] = ucwords($subjectwiseData1['categoryCode']);
                                                                
				$subjectwiseData1['totalCount'] = $subjectwiseData[0]['TotalCount'];
				$subjectwiseData1['totalCorrect'] = $subjectwiseData[0]['TotalCorrect'];
				$subjectwiseData1['perCentage'] = $subjectwiseData[0]['perCentage'];
				$subjectwiseData1['cateName'] = strtoupper($subjectwiseData[0]['stdName'].".".$subjectwiseData[0]['subject_name'].".".$gradesName.".".$subjectwiseData[0]['code']);
				$resultSubjectArray[$k] = $subjectwiseData1;
				unset($subjectwiseData1);
				$k++;
				}
			}else{
				continue;
			}
		}
		return $resultSubjectArray;
	}
	
/*public function GetQuarterlyQuestionForReport($subId, $childId, $gradeId) {
		$resultMonthArray = array();
		$monthOfYear = date('m');
$monthOfYear = 7;
if ($monthOfYear >= 1 && $monthOfYear <= 8) {
			$startYear  = date('Y')-1;
			$endYear	= date('Y');
		} else {
			$startYear  = date('Y');
			$endYear	= date('Y')+1;

}

$qdates = array();
$qdates[1] = date("$startYear-9-01 00:00:00")."&".date("$startYear-11-t 23:59:59");
$qdates[2] = date("$endYear-12-01 00:00:00")."&".date("$startYear-2-t 23:59:59");
$qdates[3] = date("$endYear-3-01 00:00:00")."&".date("$endYear-5-t 23:59:59");
$qdates[4] = date("$endYear-6-01 00:00:00")."&".date("$endYear-8-t 23:59:59");

if($monthOfYear == 9 || $monthOfYear == 10 || $monthOfYear == 11) {
			$quarter = 1;
		} else if($monthOfYear == 12 ||$monthOfYear == 1 ||$monthOfYear == 2) {
			$quarter = 2;
		} else if($monthOfYear == 3 ||$monthOfYear == 4 ||$monthOfYear == 5) {
			$quarter = 3;
		} else if($monthOfYear == 6 ||$monthOfYear == 7 ||$monthOfYear == 8) {
			$quarter = 4;
		} 
		echo "<pre>";
echo $quarter;
for ($i=$quarter;$i>=1;$i--){
$dates = explode("&",$qdates[$i]);
$startDate= $dates[0];
$endDate=$dates[1];print_r($dates);
}die;
	}*/
	
	public function checkSequenceRelatesWithSubject($currentSequence, $childId, $gradId, $subjectId,$domainId) {
		$date = date('Y-m-d');
		$where = "bchs.sequence_number = '$currentSequence'";
	 if(!empty($domainId) && $domainId != 0){
                    $whereDomain = "blc.domain_id = '$domainId'";
                }else{
                    $whereDomain = 1;
                }
		$db = Zend_Db_Table::getDefaultAdapter ();
		$select = $db->select ()->from ( array (
				'bchs' => 'bal_child_question_sequence'
		), array (
				'bchs.*'
		) )->joinLeft ( array (
				'blc' => 'bal_question_categories'
		), 'bchs.category_id = blc.category_id', array (
				'blc.category_code'
		) )->joinLeft ( array (
				'blq' => 'bal_questions' 
			), 'blq.category_id = blc.category_id', array (
				'blq.question'
		) )->where ( $where )
		->where ( "bchs.child_id = $childId" )
		->where ( "blc.subject_id = '$subjectId'" )
                ->where ( $whereDomain);
		$select->limit(1);
		$result = $db->fetchRow( $select );
		return $result;
	}
        
        /**
	 * function used to fetch question for offline mode
	 *
	 * @param
	 *        	varchar askedquestion,seq,int childId,gradeId
         * @author Suman Khatri on 3rd April 2014
	 * @return result
	 */
	public function GetExistQuestionforallsequenceForOfflineMode($seq, $askedquestion, $childId, $gradId) {
		$date = date('Y-m-d');
		if ($askedquestion != '') {
			$where = "blq.bal_question_id not in ($askedquestion) and bchs.sequence_number in ($seq)";
		} else {
			$where = "bchs.sequence_number in ($seq)";
		}
		$where1 = "(blc.subject_id = 23 and blq.expiry_date >= '$date') or (blc.subject_id != 23)";
		$db = Zend_Db_Table::getDefaultAdapter ();
		$select = $db->select ()->from ( array (
				'blq' => 'bal_questions' 
		), array (
				'blq.*' 
		) )->joinLeft ( array (
				'blqo' => 'bal_question_options' 
		), 'blq.bal_question_id = blqo.question_id', array (
				'blqo.question_option_id' 
		) )->joinLeft ( array (
				'bchs' => 'bal_child_question_sequence' 
		), 'blq.category_id = bchs.category_id', array (
				'bchs.category_id' 
		) )->joinLeft ( array (
				'blc' => 'bal_question_categories' 
		), 'blc.category_id = bchs.category_id', array (
				'blc.subject_id' 
		) )
                   ->where ( $where )
                   ->where ( "bchs.child_id = $childId" )
                   ->where ("blq.is_approved = 'Y'")
                   ->where ($where1)
                   ->where ( "blq.grade_id = '$gradId' OR blq.grade_id = 0" )
                   ->where ("blq.question_equation_images is NULL or blq.question_equation_images = ''")
                   ->where ("blq.question_equation_image_name is NULL or blq.question_equation_image_name = ''")
                   ->where ("blqo.option_equation_image_name is NULL or blqo.option_equation_image_name = ''");
                  // ->group ( "blq.bal_question_id" );
		$question = $db->fetchall ( $select );
                if(!empty($question) && $question != null){
                    return 'exist';
                }else{
                    return 'not exist';
                }
	}
        
        /**
	 * function used to fetch question for offline mode
	 *
	 * @param
	 *        	varchar askedquestion,int childId,gradeId,currentSequence
         * @author Suman Khatri on 3re April 2014
	 * @return ArrayObject
	 */
	public function GetExistQuestionForOfflineMode($currentSequence, $askedquestion, $childId, $gradId) {
		$date = date('Y-m-d');
		if ($askedquestion != '') {
			$where = "bchs.sequence_number = '$currentSequence' and blq.bal_question_id not in ($askedquestion)";
		} else {
			$where = "bchs.sequence_number = '$currentSequence'";
		}
		
		$where1 = "(blc.subject_id = 23 and blq.expiry_date >= '$date') or (blc.subject_id != 23)";
		
		$db = Zend_Db_Table::getDefaultAdapter ();
		$select = $db->select ()->from ( array (
				'blq' => 'bal_questions' 
		), array (
				'blq.*' 
		) )->joinLeft ( array (
				'blqo' => 'bal_question_options' 
		), 'blq.bal_question_id = blqo.question_id', array (
				'blqo.question_option_id'
		) )->joinLeft ( array (
				'bchs' => 'bal_child_question_sequence' 
		), 'blq.category_id = bchs.category_id', array (
				'bchs.category_id' 
		) )
		->joinLeft ( array (
				'blc' => 'bal_question_categories' 
		), 'blc.category_id = bchs.category_id', array (
				'blc.subject_id' 
		) )
                        ->where ( $where )
                        ->where ( "bchs.child_id = $childId" )
                        ->where($where1)->where("blq.is_approved = 'Y'")
                        ->where ( "blq.grade_id = '$gradId' OR blq.grade_id = 0" )
                        ->where ("blq.question_equation_images is NULL or blq.question_equation_images = ''")
                        ->where ("blq.question_equation_image_name is NULL or blq.question_equation_image_name = ''")
                        ->where ("blqo.option_equation_image_name is NULL or blqo.option_equation_image_name = ''")
                        ->group ("blqo.question_option_id" )
                        ->limit(1);
		$question = $db->fetchall ( $select );
		return $question;
	}
	public function getTotalQuestions() {
		$todayDate 					= date("Y-m-d");
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select();
		$select->from(array('this' => $this->_name),
				array("SUM(if(this.is_approved = 'Y',1,0)) AS totalQuestions",
						"SUM(if(this.is_approved = 'N',1,0)) AS totalDraftQuestions",
						"SUM(if(this.expiry_date < '$todayDate',1,0)) AS totalExpiredQuestions"));
		$result = $db->fetchRow($select);
		return $result;
	
		
	}
        
        
        /**
	 * function used to fetch question
	 *
	 * @param
	 *        	varchar askedquestion,int childId,gradeId,currentSequence
	 * @return ArrayIterator
	 */
	public function GetExistTenQuestion($currentSequence, $askedquestion, $childId, $gradId) {
		$date = date('Y-m-d');
		if ($askedquestion != '') {
			$where = "bchs.sequence_number = '$currentSequence' and blq.bal_question_id not in ($askedquestion)";
		} else {
			$where = "bchs.sequence_number = '$currentSequence'";
		}
		
		$where1 = "(blc.subject_id = 23 and blq.expiry_date >= '$date') or (blc.subject_id != 23)";
		
		$db = Zend_Db_Table::getDefaultAdapter ();
		$select = $db->select ()->from ( array (
				'blq' => 'bal_questions' 
		), array (
				'blq.*' 
		) )->joinLeft ( array (
				'bchs' => 'bal_child_question_sequence' 
		), 'blq.category_id = bchs.category_id', array (
				'bchs.category_id' 
		) )
		->joinLeft ( array (
				'blc' => 'bal_question_categories' 
		), 'blc.category_id = bchs.category_id', array (
				'blc.subject_id' 
		) )->where ( $where )->where ( "bchs.child_id = $childId" )
                        ->where($where1)->where("blq.is_approved = 'Y'")
                        ->where ( "blq.grade_id = '$gradId' OR blq.grade_id = 0" )
                        ->group ( "blq.bal_question_id" )
                        ->limit(10);
                $question = $db->fetchall ( $select );
		
		/*
		 * $db = Zend_Db_Table::getDefaultAdapter(); $select = $db->select() ->from($this->_name) ->where("category_id = '$currentSequence'") ->where("bal_question_id not in ($askedquestion)"); echo $select;die; $questionInfo = $db->fetchAll($select);
		 */
		return $question;
	}
	

        public function getApprovedQuestionCountInSameDomain($question_id) {
            $query = $this->select();
            $query->from($this, '');
            $query->columns('COUNT(DISTINCT bal_questions.bal_question_id) as count');
            $query->where('bal_questions.category_id = (SELECT category_id FROM bal_questions WHERE bal_question_id = ?)', $question_id);
            
            $query->where('is_approved = "Y"');
            $query->where('((expiry_date IS NULL) OR ((expiry_date > NOW())))');
            
            $query->group('bal_questions.category_id');
            
            return $this->fetchRow($query)->count;
        }
        
    public function getChallengeQuestion($childId, $gradId, $subjectId, $level, $domainId, $turn = 1) {

        $sendQuestionSession = new Zend_Session_Namespace('sendQuestionSession');
        $excludedQuestionsArray = array();
        
        if(!is_array($sendQuestionSession->questionData)) {
            $sendQuestionSession->questionData = array();
        }
        
        foreach ($sendQuestionSession->questionData as $a) {
            if (!empty($a['question_id'])) {
                $excludedQuestionsArray[] = $a['question_id'];
            }
        }
        $excludedQuestions = implode(',', $excludedQuestionsArray);

        /**
         * PICK A QUESTION RANDOMLY FROM DATABASE
         */
        $questionInfo = $this->GetQuestionRandomlySendChallenge($childId, $gradId, $subjectId, $level, $domainId, $excludedQuestions);

        if (!empty($questionInfo)) {
            $sendQuestionSession->questionData[] = array('question_id' => $questionInfo['bal_question_id']);
        } elseif ($turn == 1) {
            Zend_Session::namespaceUnset('sendQuestionSession');
            $questionInfo = $this->getChallengeQuestion($childId, $gradId, $subjectId, $level, $domainId, 2);
        }

        return $questionInfo;
    }
        
}	
