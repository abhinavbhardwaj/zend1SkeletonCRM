<?php
/*
 * This is a model class for Question Categories
 * Created By Sunil Khanchandani
 *
 */
class Application_Model_DbTable_QuestionCategories extends Zend_Db_Table_Abstract
{
	// This is name of Table
	protected $_name = 'bal_question_categories';



	/**
	 * function to add category 
	 * @param Array(data)
	 * @return last inserted id
	 */
	public function addCategory($data)
	{
		$options 		= $this->insert($data);
		return $options;
	}

	/**
	 * function to  update categories Data
	 * @param Array(data),int categoryid
	 * @return no of effected row
	 */
	public function updateCategoryData($data,$categoryId)
	{
		$where = $this->_db->quoteInto("category_id=?",$categoryId);
		$updateCatData 		= $this->update($data,$where);
		return $updateCatData;
	}
	/**
	 * function to check existance of category
	 * @param varchar categoryname
	 * @return true or false
	 */
	public function checkCategoryExist($categoryName)
	{
		$where = "category_code = '$categoryName'";
		$categoryExist = $this->fetchRow($where);
		if($categoryExist){
			return true;
		}elseif($categoryExist==null){
			return false;
		}
	}

	/**
	 * function to check existance of category
	 * @param varchar categoryname,categoryid
	 * @return true or false
	 */
	public function checkCategoryExistForOldCategory($categoryName,$categoryId)
	{
		$where = "category_code = '$categoryName'";
		$where .= " AND category_id = '$categoryId'"; 
		$categoryExist = $this->fetchRow($where);
		if($categoryExist){
			return true;
		}elseif($categoryExist==null){
			return false;
		}
	}


	/**
	 * function to get category info
	 * @param varchar searchData
	 * @return Array
	 */
	public function getCategoriesInfo($searchData=null)
	{
		if($searchData!=null){
				
			$where = "bl.category_code LIKE ".$this->_db->quote('%'.$searchData.'%')."";
			$where .= " OR bli.name LIKE ".$this->_db->quote('%'.$searchData.'%')."";
			$where .= " OR bl.subtopic_name LIKE ".$this->_db->quote('%'.$searchData.'%')."";
			$where .= " OR bl.psv_code LIKE ".$this->_db->quote('%'.$searchData.'%')."";
				
		}else{
			$where = 1;
		}

		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('bl' => 'bal_question_categories'),
		array('bl.*'))
		->joinLeft(array('bli' => 'bal_question_domains'),
    	 				'bl.domain_id = bli.domain_id',
		array('code','name'))
		->joinLeft(array('stnd' => 'bal_standards'),
    	 				'bl.standard_id = stnd.standard_id',
		array('stnd_name' =>'name'))
		/*->joinLeft(array('grd' => 'bal_question_category_grades'),
    	 				'bl.category_id = grd.category_id',
		array('grade_id'))*/
		->joinLeft(array('subj' => 'bal_subjects'),
    	 				'bl.subject_id = subj.subject_id',
		array('subject_name'))
		/*->joinLeft(array('grds' => 'bal_grades'),
    	 				'grd.grade_id = grds.grades_id',
		array('grade_name'))*/
		->where($where)
		//->group('grds.grade_name')
		->order('bl.modified_date desc')
		->order('bl.created_date desc'); 
		
		$childInfo =  $db->fetchAll($select);
		return $childInfo;
	}

	
	/**
	 * function to get fetch particular  category info
	 * @param int categoryId
	 * @return Array
	 */
	public function categoryInfo($categoryId, $getAllRows = FALSE)
	{
		$where = $this->_db->quoteInto("bl.category_id=?",$categoryId);
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('bl' => 'bal_question_categories'),
		array('bl.*'))
		->joinLeft(array('bli' => 'bal_question_domains'),
    	 				'bl.domain_id = bli.domain_id',
		array('code','name'))
		->joinLeft(array('stnd' => 'bal_standards'),
    	 				'bl.standard_id = stnd.standard_id',
		array('stnd_name' =>'name'))
		->joinLeft(array('grd' => 'bal_question_category_grades'),
    	 				'bl.category_id = grd.category_id',
		array('grade_id'))
		->joinLeft(array('subj' => 'bal_subjects'),
    	 				'bl.subject_id = subj.subject_id',
		array('subject_name','subject_id'))
		->joinLeft(array('grds' => 'bal_grades'),
    	 				'grd.grade_id = grds.grades_id',
		array('grade_name'))
		->where($where);
                
                if($getAllRows) {
                    $categoryInfo =  $db->fetchAll($select);
                } else {
                    $categoryInfo =  $db->fetchRow($select);
                }
		return $categoryInfo;
	}


	/**
	 * function to get fetch grade info 
	 * @param int framework_id,standardid
	 * @return Array
	 */
	public function getgradesInfo($id,$standard_id)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('bl' => 'bal_question_categories'),
		array('bl.*'))
		->joinLeft(array('bli' => 'bal_question_category_grades'),
					'bl.category_id = bli.category_id',     	 			
		array('grade_id'))
		->joinLeft(array('grds' => 'bal_grades'),
					'bli.grade_id = grds.grades_id',     	 		 
		array('grade_name','grades_id'))
		->where("bl.subject_id='$id'")
		->where("bl.standard_id='$standard_id'")
		->group('grds.grade_name')
		->order('grds.grades_id');
		$gradeInfo =  $db->fetchAll($select);
		return $gradeInfo;
	}
	/**
	 * function to get fetch domain info according to framework id
	 * @param int framework_id
	 * @return Array
	 */
	public function getdomainInfo($id)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('bl' => 'bal_question_categories'),
		array('bl.*'))
		->joinLeft(array('bld' => 'bal_question_domains'),
					'bl.domain_id = bld.domain_id',     	 				
		array('domain_id','code','name'))
		->where("bl.subject_id='$id'");
		$domainInfo =  $db->fetchAll($select);
		return $domainInfo;
	}
	/**
	 * function to get category list
	 * @param int gradeid,framework_id,standard_id,domain_id
	 * @return Array
	 */
	public function getCategory($standard_id,$framework_id,$grade_id,$domain_id) {
            $db = Zend_Db_Table::getDefaultAdapter();
            $select = $db->select();
            $select->from(array('bl' => 'bal_question_categories'), array('bl.*'));
            $select->joinLeft(array('blcg' => 'bal_question_category_grades'), 'bl.category_id = blcg.category_id', array('blcg.*'));

            if($standard_id != '') {
                $select->where("bl.standard_id = ?", $standard_id);
            }

            if($framework_id != '') {
                $select->where("bl.subject_id = ?", $framework_id);
            }

            if($domain_id != '') {
                $select->where("bl.domain_id = ?", $domain_id);
            }

            if($grade_id!='') {
                $select->where("blcg.grade_id = ?", $grade_id);                    
            }

            $select->group('bl.category_id');
            $select->order(new Zend_Db_Expr('RAND()'));
            $cateInfo =  $db->fetchAll($select);

            return $cateInfo;
	}

	/**
	 * function to get domain info
	 * @param int gradeid,framework_id,standard_id
	 * @return Array
	 */
	public function getdomainInfobygrade($id,$framework_id,$standard_id)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('blg' => 'bal_question_category_grades'),
		array('blg.*'))
		->joinLeft(array('blc' => 'bal_question_categories'),
					'blg.category_id = blc.category_id',     	 			
		array('blc.category_id'))
		->joinLeft(array('bld' => 'bal_question_domains'),
					'bld.domain_id = blc.domain_id',     	 		 
		array('bld.*'))
		->where("blg.grade_id='$id'")
		->where("blc.subject_id='$framework_id'")
		->where("blc.standard_id='$standard_id'")
		->group('bld.domain_id');
		$domainInfo =  $db->fetchAll($select);
		return $domainInfo;
	}


	/**
	 * function to delete category
	 * @param int categoryid
	 * @return int no. of affected rows
	 */
	public function deleteCategory($categoryId)
	{

		$where = $this->_db->quoteInto("category_id=?",$categoryId);
		$deleteCategory =  $this->delete($where);
		if($deleteCategory){
				
			return $this->_deleteGrade($categoryId);
				
		}
	}

	/**
	 * function to delete grade from category grade table
	 * @param int categoryid
	 * @return int no. of affected rows
	 */
	private function _deleteGrade($categoryId)
	{
		$tblGrades = new Application_Model_DbTable_QuestionGrade();
		$where = $this->_db->quoteInto("category_id=?",$categoryId);
		$deleteGrades =  $tblGrades->delete($where);
		return $deleteGrades;
		 
	}

	/**
	 * function to get detail of categorise
	 * @param int categoryid,data array to be fetch
	 * @return Array
	 */
	public function getParticulaCategoryData($categoryId,$fetchDataArray)
	{
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$where = $this->_db->quoteInto("category_id=?",$categoryId);
		$select = $db->select()
		->from($this->_name,$fetchDataArray)
		->where($where);
		$categoryData = $db->fetchRow($select);
		return $categoryData;

	}
	
	/**
	 * function to get list of categorise
	 * @param int framework_id,grade_id
	 * @return Array
	 */
	public function getCategoryByGradeandFramework($framework_id,$grade_id)
	{
		$where = '';
		if($framework_id != '')
		{
			$where = "bl.subject_id = '$framework_id'";
		}
		if($grade_id!='')
		{
			if($where != '')
			{
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
			->from(array('bl' => 'bal_question_categories'),
			array('bl.*'))
			->joinLeft(array('blcg' => 'bal_question_category_grades'),
							'bl.category_id = blcg.category_id',     	 				
			array('blcg.*'))
			->where($where)
			->where("blcg.grade_id = '$grade_id'");
			$select->group('bl.category_id');
			$cateInfo =  $db->fetchAll($select);
			}
			else
			{
				$db = Zend_Db_Table::getDefaultAdapter();
				$select = $db->select()
				->from(array('bl' => 'bal_question_categories'),
				array('bl.*'))
				->joinLeft(array('blcg' => 'bal_question_category_grades'),
							'bl.category_id = blcg.category_id',     	 				
				array('blcg.*'))
				->where("blcg.grade_id = '$grade_id'");
				$select->group('bl.category_id');
				$cateInfo =  $db->fetchAll($select);
			}
		}
		else
		{
			if($where != '')
			{
				$db = Zend_Db_Table::getDefaultAdapter();
				$select = $db->select()
				->from(array('bl' => 'bal_question_categories'),
				array('bl.*'))
				->joinLeft(array('blcg' => 'bal_question_category_grades'),
							'bl.category_id = blcg.category_id',     	 				
				array('blcg.*'))
				->where($where);
				$select->group('bl.category_id');
				$cateInfo =  $db->fetchAll($select);
				//$cateInfo = $this->fetchAll($where)->toArray();
			}
			else
			{
				$db = Zend_Db_Table::getDefaultAdapter();
				$select = $db->select()
				->from(array('bl' => 'bal_question_categories'),
				array('bl.*'))
				->joinLeft(array('blcg' => 'bal_question_category_grades'),
							'bl.category_id = blcg.category_id',     	 				
				array('blcg.*'));
				$select->group('bl.category_id');
				$cateInfo =  $db->fetchAll($select);
				//$cateInfo = $this->fetchAll()->toArray();
			}
		}
		return $cateInfo;
	}
	
	/**
	 * function to get list of categorise
	 * @param int childId
	 * @return Array
	 */
	public function getCategoryByChild($childId)
	{
		$where = "child_id = $childId";
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('blc' => 'bal_question_categories'),
		array('blc.*'))
		->joinLeft(array('blcs' => 'bal_child_question_sequence'),
						'blc.category_id = blcs.category_id',     	 				
		array('blcs.*'))
		->where($where);
		$select->group('blc.category_id');
		$cateInfo =  $db->fetchAll($select);
		return $cateInfo;
	}
	
	
/**
	 * function to get list of categorise
	 * @param int framework_id,grade_id
	 * @return Array
	 */
	public function getCategoryByGradeandFrameworkForParentLog($framework_id,$grade_id,$doimanId = NULL)
	{
		$where = '';
		if($framework_id != '')
		{
			$where = "bl.subject_id IN ($framework_id)";
		}
		if(!empty($grade_id))
		{
			if(!empty($where))
			{
				$where = $where." and blcg.grade_id IN ($grade_id)";
			}else {
				$where = "blcg.grade_id IN ($grade_id)";
			}
		}
		if(empty($where))
		{
			$where ="1";
		}
                if($doimanId != '' && $doimanId != null)
		{
			$where = "bl.domain_id IN ($doimanId)";
		}
		
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
			->from(array('bl' => 'bal_question_categories'),
			array('bl.*'))
			->joinLeft(array('blcg' => 'bal_question_category_grades'),
					'bl.category_id = blcg.category_id',     	 				
			array('blcg.*'))
			->where($where);
			
		$select->group('bl.category_id');
		$cateInfo =  $db->fetchAll($select);
		return $cateInfo;
	}
	/******************* function for get title and expaltion of the questions*********************/
	public function  getTitleExplatation($catagoryId){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('bqc' => $this->_name),
				array())
				->joinLeft(array('bs' => 'bal_subjects'),
						'bs.subject_id = bqc.subject_id',
						array('subject_name' =>'bs.subject_name'))
						->where("bqc.category_id = $catagoryId");
				$cateTitle =  $db->fetchAll($select);
				return $cateTitle;
	}
	/******************* function for get title and expaltion of the questions*********************/
	public function  getCategoryList($subjectId,$categoryCode){
		if (!empty($categoryCode)) {
			$where = "bqc.category_code LIKE'%$categoryCode%'";
		}else{
			$where = 1;
		}
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('bqc' => $this->_name),
				array('category_code'=>'DISTINCT(bqc.category_code)','subtopic_name' =>'subtopic_name','category_id' =>'bqc.category_id'))
				->joinLeft(array('bq' => 'bal_questions'),
						'bq.category_id = bqc.category_id',
						array())
						->joinLeft(array('bqd' => 'bal_question_domains'),
								'bqd.domain_id = bqc.domain_id',
								array('domain_name' =>'bqd.name'))
						->where("bqc.subject_id = $subjectId")
						->where($where);
				$data =  $db->fetchAll($select);
				$i = 0;
				foreach ($data as $dataA){
					$catId = $dataA['category_id'];
					$select1 = $db->select()
					->from(array('bq' => 'bal_questions'),
							array('total'=>'count(*)',
								"SUM(if(bq.is_approved = 'Y',1,0)) AS totalApproved"))
							->where("bq.category_id = $catId");
					$total = $db->fetchrow($select1);
					$data[$i]['total'] =  $total['total'];
					$data[$i]['totalApproved'] =  $total['totalApproved'];
					$i++;
				}
				return $data;
	}
	public function getcategoryId($categoryName){
		$where = "category_code = '$categoryName'";
		$data = $this->fetchRow($where);
		return $data;
	}
        
        /**
	 * @desc function to get grade info of a standard
	 * @param int standardid
	 * @return ArrayIterator
         * @author Suman Khatri on 21 April 2014
	 */
	public function getgradesInfoUsingStandard($standard_id)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('bl' => 'bal_question_categories'),
		array('bl.category_id'))
		->joinLeft(array('bli' => 'bal_question_category_grades'),
					'bl.category_id = bli.category_id',     	 			
		array(''))
		->joinLeft(array('grds' => 'bal_grades'),
					'bli.grade_id = grds.grades_id',     	 		 
		array('grds.grades_id','grds.grade_name'))
		->where("bl.standard_id='$standard_id'")
		->group('grds.grade_name')
		->order('grds.grades_id');
		$gradeInfo =  $db->fetchAll($select);
		return $gradeInfo;
	}
        
        /**
	 * @desc function to get subject info of a standard and grade
	 * @param int standardId,gradeId
	 * @return ArrayIterator
         * @author Suman Khatri on 21 April 2014
	 */
	public function getsubjectInfoUsingStandardAndGrade($standardId,$gradeId)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('bl' => 'bal_question_categories'),
		array('bl.category_id'))
		->joinLeft(array('bli' => 'bal_question_category_grades'),
					'bl.category_id = bli.category_id',     	 			
		array(''))
                ->joinLeft(array('sbj' => 'bal_subjects'),
					'sbj.subject_id = bl.subject_id',     	 		 
		array('sbj.*'))
		->where("bl.standard_id='$standardId'")
                ->where("bli.grade_id='$gradeId'")
		->group('sbj.subject_id')
		->order('sbj.subject_name');
		$subjectInfo =  $db->fetchAll($select);
		return $subjectInfo;
	}
	
        /**
	 * @desc function to get all domains by grade and subject
	 * @param int gradeid,framework_id,standard_id
	 * @return ArrayIterator
         * @author Suman Khatri on 19th June 2014
	 */
	public function getdomainInfobyGradeAndSubject($gradeId,$subjectId)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('blg' => 'bal_question_category_grades'),
		array(''))
		->joinLeft(array('blc' => 'bal_question_categories'),
					'blg.category_id = blc.category_id',     	 			
		array(''))
		->joinLeft(array('bld' => 'bal_question_domains'),
					'bld.domain_id = blc.domain_id',     	 		 
		array('bld.domain_id'))
		->where("blg.grade_id='$gradeId'")
		->where("blc.subject_id='$subjectId'")
		->group('bld.domain_id');
		$allDomainInfo =  $db->fetchAll($select);
		return $allDomainInfo;
	}
        
        /**
	 * @desc function to get all domains full info by grade and subject
	 * @param int gradeid,framework_id,standard_id
	 * @return ArrayIterator
         * @author Suman Khatri on 19th June 2014
	 */
	public function getdomainFullInfobyGradeAndSubject($gradeId,$subjectId)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('blg' => 'bal_question_category_grades'),
		array(''))
		->joinLeft(array('blc' => 'bal_question_categories'),
					'blg.category_id = blc.category_id',     	 			
		array(''))
		->joinLeft(array('bld' => 'bal_question_domains'),
					'bld.domain_id = blc.domain_id',     	 		 
		array('bld.domain_id','bld.code','bld.name'))
		->where("blg.grade_id='$gradeId'")
		->where("blc.subject_id='$subjectId'")
		->group('bld.domain_id');
		$allDomainInfo =  $db->fetchAll($select);
		return $allDomainInfo;
	}
}