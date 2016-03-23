<?php
/*
 * This is a model class for Child Subjects Information
 * Created By Sunil Khanchandani
 * Thursday, July 15 2013
 */
class Application_Model_DbTable_ChildSubject extends Zend_Db_Table_Abstract
{
	// This is name of Table
	protected $_name = 'bal_child_subjects';





	public function addChildSubjectInfo($data){
		return $this->insert($data);
	}


	//this function is used to fetch child info according to child id
	public function getChildSubjectsList($childId)
	{

            $db = Zend_Db_Table::getDefaultAdapter();
            $select = $db->select();
            $select->from(array('bl' => 'bal_child_subjects'),array('bl.*'));
            $select->joinLeft(array('bli' => 'bal_subjects'),'bl.subject_id = bli.subject_id');
            $select->joinLeft(array('blc' => 'bal_question_categories'),'blc.subject_id = bli.subject_id and blc.domain_id = bl.domain_id');
            $select->where('bl.child_id = ? ',$childId);
            $select->where('blc.subject_id IS NOT NULL');
            $select->group('bl.subject_id');

            $select->join(array('child' => 'bal_children'),'child.child_id = bl.child_id',array(''));
            $select->join(array('blcg' => 'bal_question_category_grades'),'blcg.category_id = blc.category_id AND child.grade_id = blcg.grade_id',array(''));
            $select->join(array('question' => 'bal_questions'),'question.category_id = blc.category_id',array(''));

            $select->where('question.is_approved = "Y"');
            $select->where('((question.expiry_date IS NULL) OR ((question.expiry_date > NOW())))');

            $childSubjectInfo =  $db->fetchAll($select);
            return $childSubjectInfo;
            
	}
	//function to fetch subject of child
	public function getChildSubject($childId)
	{
		$where = "child_id = $childId";
		return $this->fetchAll($where);
	}

	/**
	 * This is a function to update child Subject Info
	 */
	public function updateChildSubjectInfo($updateData,$subjectId,$childID)
	{
		$where = $this->_db->quoteInto("child_subject_id = ?",$subjectId);
		$where .= $this->_db->quoteInto(" AND child_id = ?",$childID);
		return $this->update($updateData, $where);
	}


	//function to fetch subject of child
	public function getAllChildSubject($childId)
	{
		$where = "child_id = $childId";
		return $this->fetchAll($where)->toArray();
	}
	//function to fetch subject of child
	public function getChildSubjectOnSubId($subjectId)
	{
		$where = "subject_id = $subjectId";
		return $this->fetchRow($where)->toArray();
	}
	/***************
	 * function for delete child subjects
	* @param childId Int
	* return int
	*
	* *************************/
	public function removeChildSubjectOnChildId($childId)
	{
		$where = $this->_db->quoteInto("child_id = ?",$childId);
		return $this->delete($where);
	}


	public function getSubjectDataOnFrameworkName($frameWork,$fetchDataArray){


		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$select = $db->select()
		->from('bal_subjects',$fetchDataArray)
		->where("subject_name ='$frameWork'");
		$subjectData = $db->fetchRow($select);
		return $subjectData;

	}

	public function getSubjectDataOnSubjectId($subjectId){
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$select = $db->select()
		->from('bal_subjects','*')
		->where("subject_id ='$subjectId'");
		$subjectData = $db->fetchRow($select);
		return $subjectData;

	}
	public function getAllSubjectList(){
                $db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$select = $db->select()
		->from('bal_subjects','*')
		->order("standard_id");
		/*$subjectData = $db->fetchRow($select);
		return $subjectData;
		$this->_name = 'bal_subjects';*/
		$subjectList = $db->fetchAll($select);
		return $subjectList;

	}

	public function addSubjectInfo($data){
		$this->_name = 'bal_subjects';
		return $this->insert($data);
	}
        public function updateSubjectInfo($data,$where){
                return $this->update($data,$where);
	}
/********************get subject id 
 * @param name 
 * @return array
 * ************************/	
	public function getAllData($subjectName){
		$this->_name = 'bal_subjects';
		$where = "subject_name LIKE '%$subjectName%' ";
		$result = $this->fetchAll($where);
		$this->_name = 'bal_child_subjects';
		return $result;
	}
        
        
        /**
	 * @desc function to get subject list
	 * @param int gradeId,$childId
         * @author suman khatri on 21 June 2014
	 * @return Array list
	 */
	public function getChildSubjectListWithDomain($childId,$gradeId){
            $db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('blcs' => 'bal_child_subjects'),
		array('blcs.subject_id'))
		->joinLeft(array('bls' => 'bal_subjects'),
                    'bls.subject_id = blcs.subject_id',     	 				
		array('bls.subject_name'))
                ->joinLeft(array('bld' => 'bal_question_domains'),
			'blcs.domain_id = bld.domain_id',     	 				
		array('bld.domain_id'))
		//->where("blg.grades_id='$gradeId'")
                ->where("blcs.child_id='$childId'")
		//->where("blst.standard_id='".STANDARD."'")
		//->group('bls.subject_id');
                ->group(array('bld.domain_id','bls.subject_name'));
		$subjectList =  $db->fetchAll($select);
		return $subjectList;		
	}
        
        /**
	 * @desc function to get domain list
	 * @param int $childId,$subjectId
         * @author suman khatri on 25 June 2014
	 * @return Array list
	 */
	public function getDomainListSubjectAndChildWise($childId,$subjectId){

            /*$db = Zend_Db_Table::getDefaultAdapter();
            $select = $db->select();
            $select->from(array('blcg' => 'bal_question_category_grades'),array(''));
            $select->join(array('blc' => 'bal_question_categories'),'blcg.category_id = blc.category_id',array(''));
            $select->join(array('blg' => 'bal_grades'),'blg.grades_id = blcg.grade_id',array(''));
            $select->join(array('bls' => 'bal_subjects'),'bls.subject_id = blc.subject_id',array('bls.subject_id','bls.subject_name'));
            $select->join(array('blst' => 'bal_standards'),'blst.standard_id = blc.standard_id',array(''));
            $select->join(array('bld' => 'bal_question_domains'),'blc.domain_id = bld.domain_id',array('bld.*'));
            $select->join(array('question' => 'bal_questions'),'question.category_id = blc.category_id',array(''));
            $select->where("blg.grades_id='$gradeId'");

            $select->group(array('bld.domain_id','bls.subject_name'));

            $select->where('question.is_approved = "Y"');
            $select->where('((question.expiry_date IS NULL) OR ((question.expiry_date > NOW())))');
            
            echo $select; exit;

            $subjectList =  $db->fetchAll($select);
            return $subjectList;*/
            
            $db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('blcs' => 'bal_child_subjects'),
		array())
		->joinLeft(array('bld' => 'bal_question_domains'),
			'blcs.domain_id = bld.domain_id',     	 				
		array('bld.domain_id','bld.name','bld.code'))
                ->joinLeft(array('blc' => 'bal_question_categories'),
			'blc.domain_id = bld.domain_id',     	 				
		array('blc.domain_id'))        
                ->where("blcs.child_id='$childId'")
		->where("blcs.subject_id = $subjectId")
                ->where("blc.domain_id IS NOT NULL")
                ->group('bld.domain_id');
                
                
                $select->join(array('child' => 'bal_children'),'child.child_id = blcs.child_id',array(''));
                $select->join(array('blcg' => 'bal_question_category_grades'),'blcg.category_id = blc.category_id AND child.grade_id = blcg.grade_id',array(''));
                $select->join(array('blg' => 'bal_grades'),'blg.grades_id = blcg.grade_id',array(''));
                $select->join(array('question' => 'bal_questions'),'question.category_id = blc.category_id',array(''));
                
                
                $select->where('question.is_approved = "Y"');
                $select->where('((question.expiry_date IS NULL) OR ((question.expiry_date > NOW())))');
		$domainList =  $db->fetchAll($select);
		return $domainList;		
	}

}